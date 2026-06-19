<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Renewal;
use App\Models\Service;
use Illuminate\Http\Request;

class RenewalController extends Controller
{
    public function index(Request $request)
    {
        $query = Renewal::with('client', 'service');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn ($q) => $q->where('description', 'like', "%$s%")
                ->orWhereHas('client', fn ($q2) => $q2->where('company_name', 'like', "%$s%")));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        $filter = $request->get('filter', 'upcoming');
        if ($filter === 'upcoming') {
            $query->where('renewal_date', '<=', now()->addDays(90))->where('status', 'pending');
        } elseif ($filter === 'overdue') {
            $query->where('renewal_date', '<', now())->where('status', 'pending');
        }

        $renewals = $query->orderBy('renewal_date')->paginate(25)->withQueryString();
        $clients  = Client::orderBy('company_name')->pluck('company_name', 'id');

        return view('renewals.index', compact('renewals', 'clients', 'filter'));
    }

    public function create(Request $request)
    {
        $clients  = Client::where('status', 'active')->orderBy('company_name')->get();
        $services = Service::where('is_active', true)->orderBy('name')->get();
        $selected_client = $request->client_id;

        return view('renewals.create', compact('clients', 'services', 'selected_client'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id'        => 'required|exists:clients,id',
            'service_id'       => 'nullable|exists:services,id',
            'description'      => 'required|string|max:255',
            'renewal_date'     => 'required|date',
            'amount'           => 'nullable|numeric|min:0',
            'status'           => 'required|in:pending,renewed,cancelled,overdue',
            'billing_cycle'    => 'required|in:monthly,quarterly,annually,one_off',
            'next_renewal_date' => 'nullable|date',
            'notes'            => 'nullable|string',
        ]);

        Renewal::create($data);

        return redirect()->route('renewals.index')->with('success', 'Renewal created.');
    }

    public function show(Renewal $renewal)
    {
        $renewal->load('client', 'service');

        return view('renewals.show', compact('renewal'));
    }

    public function edit(Renewal $renewal)
    {
        $clients  = Client::where('status', 'active')->orderBy('company_name')->get();
        $services = Service::where('is_active', true)->orderBy('name')->get();

        return view('renewals.edit', compact('renewal', 'clients', 'services'));
    }

    public function update(Request $request, Renewal $renewal)
    {
        $data = $request->validate([
            'client_id'        => 'required|exists:clients,id',
            'service_id'       => 'nullable|exists:services,id',
            'description'      => 'required|string|max:255',
            'renewal_date'     => 'required|date',
            'amount'           => 'nullable|numeric|min:0',
            'status'           => 'required|in:pending,renewed,cancelled,overdue',
            'billing_cycle'    => 'required|in:monthly,quarterly,annually,one_off',
            'next_renewal_date' => 'nullable|date',
            'notes'            => 'nullable|string',
        ]);

        $renewal->update($data);

        return redirect()->route('renewals.index')->with('success', 'Renewal updated.');
    }

    public function destroy(Renewal $renewal)
    {
        abort_unless(auth()->user()->isManager(), 403);
        $renewal->delete();

        return redirect()->route('renewals.index')->with('success', 'Renewal deleted.');
    }
}
