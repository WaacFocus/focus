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
        $letters = EngagementLetter::with('client', 'sentBy')
            ->orderByDesc('created_at')
            ->paginate(25);

        return view('engagement-letters.index', compact('letters'));
    }

    public function create(Request $request)
    {
        $templates = EngagementLetterTemplate::where('is_active', true)->orderBy('sort_order')->get();
        $clients   = Client::where('status', 'active')->orderBy('company_name')->get();
        $renewal   = $request->filled('renewal_id') ? Renewal::find($request->renewal_id) : null;
        $letter    = new EngagementLetter();

        $clientServiceTypes = collect();

        if ($request->filled('client_id')) {
            $preClient = Client::with('services')->find($request->client_id);
            if ($preClient) {
                $letter->client_id  = (int) $request->client_id;
                $letter->subject    = 'Engagement Letter — ' . $preClient->company_name;
                $clientServiceTypes = $preClient->services->map(fn($s) => strtolower($s->name));
            }
        }

        return view('engagement-letters.builder', compact('templates', 'clients', 'renewal', 'letter', 'clientServiceTypes'));
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
            return $this->doSend($letter, $client);
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
            return $this->doSend($engagementLetter, $client);
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

    private function doSend(EngagementLetter $letter, Client $client): \Illuminate\Http\RedirectResponse
    {
        if (!$client->email) {
            return redirect()->back()->with('error', 'This client has no email address on record.');
        }

        $token       = Str::uuid()->toString();
        $composedHtml = $this->composeHtml($letter, $client);
        $signingUrl  = route('sign.show', $token);

        $letter->update([
            'token'        => $token,
            'composed_html'=> $composedHtml,
            'status'       => 'sent',
            'sent_at'      => now(),
            'sent_by'      => auth()->id(),
        ]);

        $smtp     = app(Smtp2goService::class);
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

        return redirect()->route('engagement-letters.show', $letter)
            ->with('success', 'Engagement letter sent to ' . $client->email . '.');
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
