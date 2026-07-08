<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientBillingLine;
use App\Models\ClientType;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::with('clientType');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn ($q) => $q->where('company_name', 'like', "%$s%")
                ->orWhere('contact_first_name', 'like', "%$s%")
                ->orWhere('contact_last_name', 'like', "%$s%")
                ->orWhere('email', 'like', "%$s%"));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $clients     = $query->orderBy('company_name')->paginate(20)->withQueryString();
        $clientTypes = ClientType::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();

        return view('clients.index', compact('clients', 'clientTypes'));
    }

    public function create()
    {
        $client      = new Client();
        $clientTypes = ClientType::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        return view('clients.create', compact('client', 'clientTypes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_code'                => 'required|string|max:50',
            'company_name'               => 'required|string|max:255',
            'client_type_id'             => 'required|exists:client_types,id',
            'contact_title'              => 'nullable|string|max:20',
            'contact_first_name'         => 'nullable|string|max:100',
            'contact_middle_name'        => 'nullable|string|max:100',
            'contact_last_name'          => 'nullable|string|max:100',
            'email'                      => 'nullable|email|max:255',
            'phone'                      => 'nullable|string|max:50',
            'premises'                   => 'nullable|string|max:255',
            'premises_town'              => 'nullable|string|max:100',
            'premises_county'            => 'nullable|string|max:100',
            'premises_postcode'          => 'nullable|string|max:20',
            'vat_number'                 => 'nullable|string|max:50',
            'company_number'             => 'nullable|string|max:50',
            'utr_number'                 => 'nullable|string|max:50',
            'paye_ref'                   => 'nullable|string|max:50',
            'status'                     => 'required|in:active,inactive,prospect',
            'account_manager'            => 'nullable|string|max:100',
            'notes'                      => 'nullable|string',
            'fpa_year_end'               => 'nullable|date',
            'fpa_amount'                 => 'nullable|numeric|min:0',
            'billing_interval'           => 'nullable|in:monthly,quarterly,annually,one-off',
            'payment_method'                          => 'nullable|string|max:100',
            'ch_status'                               => 'nullable|string|max:100',
            'ch_company_type'                         => 'nullable|string|max:100',
            'ch_incorporated_on'                      => 'nullable|date',
            'ch_jurisdiction'                         => 'nullable|string|max:100',
            'ch_sic_codes'                            => 'nullable|string|max:255',
            'ch_reg_address_line_1'                   => 'nullable|string|max:255',
            'ch_reg_address_line_2'                   => 'nullable|string|max:255',
            'ch_reg_locality'                         => 'nullable|string|max:100',
            'ch_reg_region'                           => 'nullable|string|max:100',
            'ch_reg_postcode'                         => 'nullable|string|max:20',
            'ch_reg_country'                          => 'nullable|string|max:100',
            'ch_accounts_year_end'                    => 'nullable|date',
            'ch_accounts_next_due'                    => 'nullable|date',
            'ch_confirmation_statement_next_due'      => 'nullable|date',
            'billing_lines'                           => 'nullable|array',
            'billing_lines.*.description'=> 'nullable|string|max:255',
            'billing_lines.*.amount'     => 'nullable|numeric|min:0',
            'billing_lines.*.interval'   => 'nullable|in:monthly,quarterly,annually,one-off',
        ]);


        $lines = $data['billing_lines'] ?? [];
        unset($data['billing_lines']);

        foreach (['premises' => 'address', 'premises_town' => 'town', 'premises_county' => 'county', 'premises_postcode' => 'postcode'] as $from => $to) {
            if (array_key_exists($from, $data)) { $data[$to] = $data[$from]; unset($data[$from]); }
        }

        $client = Client::create($data);
        $this->saveBillingLines($client, $lines);
        $this->saveDirectors($client, $request->input('directors_json'));

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Client created successfully.', 'id' => $client->id]);
        }

        return redirect()->route('clients.show', $client)->with('success', 'Client created successfully.');
    }

    public function show(Request $request, Client $client)
    {
        if ($request->expectsJson()) {
            $client->load(['billingLines', 'directors']);
            return response()->json($client);
        }

        $client->load(['clientType', 'billingLines', 'services', 'renewals.service', 'jobs.assignedTo', 'directors', 'engagementLetters']);

        $availableServices = Service::where('is_active', true)
            ->whereNotIn('id', $client->services->pluck('id'))
            ->orderBy('name')
            ->get();

        $users       = User::orderBy('name')->get(['id', 'name']);
        $clientTypes = ClientType::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();

        return view('clients.show', compact('client', 'availableServices', 'users', 'clientTypes'));
    }

    public function edit(Client $client)
    {
        $client->load('billingLines');
        $services    = Service::where('is_active', true)->orderBy('name')->get();
        $clientTypes = ClientType::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();

        return view('clients.edit', compact('client', 'services', 'clientTypes'));
    }

    public function update(Request $request, Client $client)
    {
        $data = $request->validate([
            'client_code'                => 'required|string|max:50',
            'company_name'               => 'required|string|max:255',
            'client_type_id'             => 'required|exists:client_types,id',
            'contact_title'              => 'nullable|string|max:20',
            'contact_first_name'         => 'nullable|string|max:100',
            'contact_middle_name'        => 'nullable|string|max:100',
            'contact_last_name'          => 'nullable|string|max:100',
            'email'                      => 'nullable|email|max:255',
            'phone'                      => 'nullable|string|max:50',
            'premises'                   => 'nullable|string|max:255',
            'premises_town'              => 'nullable|string|max:100',
            'premises_county'            => 'nullable|string|max:100',
            'premises_postcode'          => 'nullable|string|max:20',
            'vat_number'                 => 'nullable|string|max:50',
            'company_number'             => 'nullable|string|max:50',
            'utr_number'                 => 'nullable|string|max:50',
            'paye_ref'                   => 'nullable|string|max:50',
            'status'                     => 'required|in:active,inactive,prospect',
            'account_manager'            => 'nullable|string|max:100',
            'notes'                      => 'nullable|string',
            'fpa_year_end'               => 'nullable|date',
            'fpa_amount'                 => 'nullable|numeric|min:0',
            'billing_interval'           => 'nullable|in:monthly,quarterly,annually,one-off',
            'payment_method'                          => 'nullable|string|max:100',
            'ch_status'                               => 'nullable|string|max:100',
            'ch_company_type'                         => 'nullable|string|max:100',
            'ch_incorporated_on'                      => 'nullable|date',
            'ch_jurisdiction'                         => 'nullable|string|max:100',
            'ch_sic_codes'                            => 'nullable|string|max:255',
            'ch_reg_address_line_1'                   => 'nullable|string|max:255',
            'ch_reg_address_line_2'                   => 'nullable|string|max:255',
            'ch_reg_locality'                         => 'nullable|string|max:100',
            'ch_reg_region'                           => 'nullable|string|max:100',
            'ch_reg_postcode'                         => 'nullable|string|max:20',
            'ch_reg_country'                          => 'nullable|string|max:100',
            'ch_accounts_year_end'                    => 'nullable|date',
            'ch_accounts_next_due'                    => 'nullable|date',
            'ch_confirmation_statement_next_due'      => 'nullable|date',
            'billing_lines'                           => 'nullable|array',
            'billing_lines.*.description'=> 'nullable|string|max:255',
            'billing_lines.*.amount'     => 'nullable|numeric|min:0',
            'billing_lines.*.interval'   => 'nullable|in:monthly,quarterly,annually,one-off',
        ]);


        $lines = $data['billing_lines'] ?? [];
        unset($data['billing_lines']);

        foreach (['premises' => 'address', 'premises_town' => 'town', 'premises_county' => 'county', 'premises_postcode' => 'postcode'] as $from => $to) {
            if (array_key_exists($from, $data)) { $data[$to] = $data[$from]; unset($data[$from]); }
        }

        $client->update($data);
        $this->saveBillingLines($client, $lines);
        $this->saveDirectors($client, $request->input('directors_json'), true);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Client updated successfully.', 'id' => $client->id]);
        }

        return redirect()->route('clients.show', $client)->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client)
    {
        abort_unless(auth()->user()->isManager(), 403);

        // Remove this person from any company's director list (matched by name)
        \App\Models\ClientDirector::whereRaw('LOWER(name) = ?', [strtolower(trim($client->company_name))])->delete();

        $client->jobs()->delete();
        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Client deleted.');
    }

    public function createSaJob(Client $client)
    {
        $nextJan31 = \Carbon\Carbon::create(now()->year, 1, 31)->startOfDay();
        if (! $nextJan31->isFuture()) {
            $nextJan31->addYear();
        }

        \App\Models\Job::create([
            'name'        => 'Self Assessment',
            'client_id'   => $client->id,
            'assigned_to' => auth()->id(),
            'frequency'   => 'yearly',
            'due_date'    => $nextJan31,
            'status'      => 'pending',
        ]);

        return back()->with('success', 'Self Assessment job created — due ' . $nextJan31->format('d M Y') . '.');
    }

    public function destroyDirector(Client $client, \App\Models\ClientDirector $director)
    {
        abort_unless($director->client_id === $client->id, 404);
        $director->delete();
        return back()->with('success', $director->name . ' removed from directors.');
    }

    private function saveDirectors(Client $client, ?string $json, bool $replace = false): void
    {
        if (! $json) return;
        $directors = json_decode($json, true);
        if (! is_array($directors)) return;

        if ($replace) {
            $client->directors()->delete();
        }

        foreach ($directors as $d) {
            $client->directors()->create([
                'name'                 => $d['name'] ?? '',
                'role'                 => $d['role'] ?? 'director',
                'appointed_on'         => $d['appointed_on'] ?: null,
                'dob_month'            => $d['dob_month'] ?? null,
                'dob_year'             => $d['dob_year'] ?? null,
                'nationality'          => $d['nationality'] ?? null,
                'occupation'           => $d['occupation'] ?? null,
                'country_of_residence' => $d['country_of_residence'] ?? null,
                'sa_required'          => ! empty($d['sa_required']),
            ]);

            if (! empty($d['create_as_client']) && ! empty($d['client_code'])) {
                $this->createClientFromOfficer($d);
            }
        }
    }

    private function createClientFromOfficer(array $d): void
    {
        $fullName   = trim($d['name'] ?? '');
        $parts      = array_values(array_filter(explode(' ', $fullName)));
        $lastName   = count($parts) > 1 ? array_pop($parts) : '';
        $firstName  = array_shift($parts) ?? $fullName;
        $middleName = implode(' ', $parts);

        try {
            $typeId = $d['individual_type_id'] ?? null;
            if (! $typeId) {
                $typeId = \App\Models\ClientType::whereRaw('LOWER(name) LIKE ?', ['%individual%'])->value('id');
            }

            $newClient = Client::create([
                'client_code'          => $d['client_code'],
                'company_name'         => $fullName,
                'contact_first_name'   => $firstName,
                'contact_middle_name'  => $middleName ?: null,
                'contact_last_name'    => $lastName,
                'status'               => 'active',
                'client_type_id'       => $typeId,
            ]);

            if (! empty($d['sa_required'])) {
                // Assign Self Assessment service if it exists
                $saService = \App\Models\Service::whereRaw('LOWER(name) LIKE ?', ['%self assessment%'])
                    ->where('is_active', true)
                    ->first();
                if ($saService) {
                    $newClient->services()->attach($saService->id, [
                        'start_date' => now()->format('Y-m-d'),
                    ]);
                }

                // Create a yearly SA job due on the next 31st January
                $nextJan31 = \Carbon\Carbon::create(now()->year, 1, 31)->startOfDay();
                if (! $nextJan31->isFuture()) {
                    $nextJan31->addYear();
                }

                \App\Models\Job::create([
                    'name'        => 'Self Assessment',
                    'client_id'   => $newClient->id,
                    'assigned_to' => auth()->id(),
                    'frequency'   => 'yearly',
                    'due_date'    => $nextJan31,
                    'status'      => 'pending',
                ]);
            }
        } catch (\Throwable) {
            // Duplicate client_code or other DB constraint — skip silently
        }
    }

    private function saveBillingLines(Client $client, array $lines): void
    {
        $client->billingLines()->delete();
        foreach ($lines as $line) {
            $amount = $line['amount'] ?? null;
            if ($amount !== null && $amount !== '') {
                $client->billingLines()->create([
                    'description' => $line['description'] ?? null,
                    'amount'      => $amount,
                    'interval'    => $line['interval'] ?? 'monthly',
                ]);
            }
        }
    }
}
