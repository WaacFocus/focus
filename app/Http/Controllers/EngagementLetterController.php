<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\EngagementLetter;
use App\Models\EngagementLetterTemplate;
use App\Models\Renewal;
use App\Models\User;
use App\Services\Smtp2goService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EngagementLetterController extends Controller
{
    public function index()
    {
        $perPage = in_array((int) request('per_page'), [25, 50, 100, 250]) ? (int) request('per_page') : 25;
        $letters = EngagementLetter::with('client', 'sentBy')
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();

        return view('engagement-letters.index', compact('letters'));
    }

    public function create(Request $request)
    {
        $templates = EngagementLetterTemplate::where('is_active', true)->orderBy('sort_order')->get();
        $clients   = Client::where('status', 'active')->orderBy('company_name')->get();
        $renewal   = $request->filled('renewal_id') ? Renewal::find($request->renewal_id) : null;
        $letter    = new EngagementLetter();

        $autoIncludeIds = collect();

        if ($request->filled('client_id')) {
            $preClient = Client::with('services')->find($request->client_id);
            if ($preClient) {
                $letter->client_id = (int) $request->client_id;
                $letter->subject   = 'Engagement Letter — ' . $preClient->company_name;

                $serviceNames = $preClient->services->map(fn($s) => strtolower(trim($s->name)))->filter()->values();

                if ($serviceNames->isNotEmpty()) {
                    $autoIncludeIds = EngagementLetterTemplate::where('is_active', true)
                        ->where(function ($q) use ($serviceNames) {
                            $q->whereIn('service_type', $serviceNames->toArray());
                            foreach ($serviceNames as $name) {
                                $q->orWhereRaw('LOWER(TRIM(title)) = ?', [$name]);
                            }
                        })
                        ->pluck('id');
                }
            }
        }

        return view('engagement-letters.builder', compact('templates', 'clients', 'renewal', 'letter', 'autoIncludeIds'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id'     => 'required|exists:clients,id',
            'subject'       => 'required|string|max:255',
            'sections_json' => 'required|json',
        ]);

        $sections = json_decode($request->sections_json, true);
        $client   = Client::find($request->client_id);
        $action   = $request->input('action', 'draft');

        $letter = EngagementLetter::create([
            'client_id'  => $request->client_id,
            'renewal_id' => $request->renewal_id ?: null,
            'subject'    => $request->subject,
            'sections'   => $sections,
            'status'     => 'draft',
        ]);

        if ($action === 'send') {
            if (!$client->email) {
                return redirect()->back()->with('error', 'This client has no email address on record.');
            }
            try {
                $this->executeSend($letter, $client);
            } catch (\Throwable $e) {
                return redirect()->back()->with('error', 'Failed to send: ' . $e->getMessage());
            }
            if ($this->getDirectorClients($client)->isNotEmpty()) {
                return redirect()->route('engagement-letters.directors', $letter)
                    ->with('success', 'Engagement letter sent to ' . $client->company_name . '.');
            }
            return redirect()->route('engagement-letters.show', $letter)
                ->with('success', 'Engagement letter sent to ' . $client->email . '.');
        }

        return redirect()->route('engagement-letters.show', $letter)
            ->with('success', 'Draft saved.');
    }

    public function show(EngagementLetter $engagementLetter)
    {
        $engagementLetter->load('client', 'renewal', 'sentBy');
        return view('engagement-letters.show', ['letter' => $engagementLetter]);
    }

    public function edit(EngagementLetter $engagementLetter)
    {
        abort_if($engagementLetter->status === 'signed', 403, 'Signed letters cannot be edited.');

        $templates = EngagementLetterTemplate::where('is_active', true)->orderBy('sort_order')->get();
        $clients   = Client::where('status', 'active')->orderBy('company_name')->get();
        $renewal   = $engagementLetter->renewal;
        $letter    = $engagementLetter;

        return view('engagement-letters.builder', compact('templates', 'clients', 'renewal', 'letter'));
    }

    public function update(Request $request, EngagementLetter $engagementLetter)
    {
        abort_if($engagementLetter->status === 'signed', 403);

        $request->validate([
            'client_id'     => 'required|exists:clients,id',
            'subject'       => 'required|string|max:255',
            'sections_json' => 'required|json',
        ]);

        $sections = json_decode($request->sections_json, true);
        $client   = Client::find($request->client_id);
        $action   = $request->input('action', 'draft');

        $engagementLetter->update([
            'client_id'  => $request->client_id,
            'renewal_id' => $request->renewal_id ?: null,
            'subject'    => $request->subject,
            'sections'   => $sections,
        ]);

        if ($action === 'send') {
            if (!$client->email) {
                return redirect()->back()->with('error', 'This client has no email address on record.');
            }
            try {
                $this->executeSend($engagementLetter, $client);
            } catch (\Throwable $e) {
                return redirect()->back()->with('error', 'Failed to send: ' . $e->getMessage());
            }
            if ($this->getDirectorClients($client)->isNotEmpty()) {
                return redirect()->route('engagement-letters.directors', $engagementLetter)
                    ->with('success', 'Engagement letter sent to ' . $client->company_name . '.');
            }
            return redirect()->route('engagement-letters.show', $engagementLetter)
                ->with('success', 'Engagement letter sent to ' . $client->email . '.');
        }

        return redirect()->route('engagement-letters.show', $engagementLetter)
            ->with('success', 'Draft updated.');
    }

    public function send(Request $request, EngagementLetter $engagementLetter)
    {
        abort_if($engagementLetter->status === 'signed', 403);
        $client = $engagementLetter->client;
        return $this->doSend($engagementLetter, $client);
    }

    public function pdf(EngagementLetter $engagementLetter)
    {
        $engagementLetter->load('client');
        $pdf      = Pdf::loadView('pdf.engagement-letter', ['letter' => $engagementLetter])->setPaper('A4', 'portrait');
        $filename = 'engagement-letter-' . $engagementLetter->client->company_name . '-' . now()->format('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }

    public function destroy(EngagementLetter $engagementLetter)
    {
        abort_unless(auth()->user()->isManager(), 403);
        $engagementLetter->delete();
        return redirect()->route('engagement-letters.index')->with('success', 'Letter deleted.');
    }

    public function directorLetters(EngagementLetter $engagementLetter): \Illuminate\Contracts\View\View
    {
        $engagementLetter->load('client');
        $company = $engagementLetter->client;

        $directorClients = $this->getDirectorClients($company)->map(function ($item) {
            $item['sent_letter'] = EngagementLetter::where('client_id', $item['client']->id)
                ->whereYear('created_at', now()->year)
                ->where('status', '!=', 'draft')
                ->latest()
                ->first();
            return $item;
        });

        return view('engagement-letters.director-letters', [
            'letter'          => $engagementLetter,
            'company'         => $company,
            'directorClients' => $directorClients,
        ]);
    }

    public function sendDirectorLetter(EngagementLetter $engagementLetter, Client $client): \Illuminate\Http\JsonResponse
    {
        $templates = EngagementLetterTemplate::where('is_active', true)
            ->where(function ($q) {
                $q->where('is_mandatory', true)
                  ->orWhere('title', 'Self Assessment');
            })
            ->orderBy('sort_order')
            ->get();

        $sections = $templates->map(fn($t) => [
            'template_id' => $t->id,
            'title'       => $t->title,
            'body'        => $t->body,
        ])->values()->toArray();

        $letter = EngagementLetter::create([
            'client_id' => $client->id,
            'subject'   => 'Engagement Letter — ' . $client->company_name,
            'sections'  => $sections,
            'status'    => 'draft',
        ]);

        try {
            $this->executeSend($letter, $client);
        } catch (\Throwable $e) {
            $letter->delete();
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response()->json(['success' => true, 'letter_id' => $letter->id]);
    }

    private function doSend(EngagementLetter $letter, Client $client): \Illuminate\Http\RedirectResponse
    {
        if (!$client->email) {
            return redirect()->back()->with('error', 'This client has no email address on record.');
        }

        try {
            $this->executeSend($letter, $client);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Failed to send: ' . $e->getMessage());
        }

        if ($this->getDirectorClients($client)->isNotEmpty()) {
            return redirect()->route('engagement-letters.directors', $letter)
                ->with('success', 'Engagement letter sent to ' . $client->company_name . '.');
        }

        return redirect()->route('engagement-letters.show', $letter)
            ->with('success', 'Engagement letter sent to ' . $client->email . '.');
    }

    private function executeSend(EngagementLetter $letter, Client $client): void
    {
        $token        = Str::uuid()->toString();
        $composedHtml = $this->composeHtml($letter, $client);
        $signingUrl   = route('sign.show', $token);

        $letter->update([
            'token'         => $token,
            'composed_html' => $composedHtml,
            'status'        => 'sent',
            'sent_at'       => now(),
            'sent_by'       => auth()->id(),
        ]);

        $smtp      = app(Smtp2goService::class);
        $emailHtml = view('emails.engagement-letter-request', [
            'client'     => $client,
            'letter'     => $letter,
            'signingUrl' => $signingUrl,
        ])->render();

        $smtp->send(
            $client->email,
            $client->contact_name ?: $client->company_name,
            $letter->subject,
            $emailHtml,
            'Woods Accounting & Consulting'
        );
    }

    private function getDirectorClients(Client $company): \Illuminate\Support\Collection
    {
        return $company->directors()
            ->where('sa_required', true)
            ->whereNull('resigned_on')
            ->get()
            ->map(function ($director) {
                $client = Client::whereRaw('LOWER(company_name) = ?', [strtolower(trim($director->name))])
                    ->where('status', 'active')
                    ->first();
                return $client ? ['director' => $director, 'client' => $client] : null;
            })
            ->filter()
            ->values();
    }

    private function composeHtml(EngagementLetter $letter, Client $client): string
    {
        $sections = $letter->sections;
        $html     = '';
        foreach ($sections as $section) {
            $html .= '<h3 style="font-size:14px;margin-top:20px;color:#0C3D38;">' . e($section['title']) . '</h3>';
            $html .= '<p style="margin:0 0 12px;white-space:pre-line;">' . e($section['body']) . '</p>';
        }
        return $html;
    }
}
