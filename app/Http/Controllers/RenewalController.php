<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Renewal;
use Illuminate\Http\Request;

class RenewalController extends Controller
{
    public function index(Request $request)
    {
        $query = Renewal::with('client');

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
            $query->where('due_date', '<=', now()->addDays(90))->whereIn('status', ['pending', 'sent']);
        } elseif ($filter === 'overdue') {
            $query->where('due_date', '<', now())->whereIn('status', ['pending', 'sent']);
        }

        $perPage  = in_array((int) $request->input('per_page'), [25, 50, 100, 250]) ? (int) $request->input('per_page') : 25;
        $renewals = $query->orderBy('due_date')->paginate($perPage)->withQueryString();
        $clients  = Client::orderBy('company_name')->pluck('company_name', 'id');

        return view('renewals.index', compact('renewals', 'clients', 'filter'));
    }

    public function create(Request $request)
    {
        $clients         = Client::where('status', 'active')->orderBy('company_name')->get();
        $selected_client = $request->client_id;

        return view('renewals.create', compact('clients', 'selected_client'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id'      => 'required|exists:clients,id',
            'description'    => 'required|string|max:255',
            'completed_date' => 'nullable|date',
            'due_date'       => 'nullable|date',
            'status'         => 'required|in:pending,sent,signed,overdue',
            'notes'          => 'nullable|string',
        ]);

        // Auto-set due_date 12 months from completed_date when signed
        if (!empty($data['completed_date']) && empty($data['due_date'])) {
            $data['due_date'] = \Carbon\Carbon::parse($data['completed_date'])->addYear()->toDateString();
        }
        if (!empty($data['completed_date'])) {
            $data['status'] = 'signed';
        }

        Renewal::create($data);

        return redirect()->route('renewals.index')->with('success', 'Engagement letter created.');
    }

    public function show(Renewal $renewal)
    {
        $renewal->load('client');

        return view('renewals.show', compact('renewal'));
    }

    public function edit(Renewal $renewal)
    {
        $clients = Client::where('status', 'active')->orderBy('company_name')->get();

        return view('renewals.edit', compact('renewal', 'clients'));
    }

    public function update(Request $request, Renewal $renewal)
    {
        $data = $request->validate([
            'client_id'      => 'required|exists:clients,id',
            'description'    => 'required|string|max:255',
            'completed_date' => 'nullable|date',
            'due_date'       => 'nullable|date',
            'status'         => 'required|in:pending,sent,signed,overdue',
            'notes'          => 'nullable|string',
        ]);

        // Auto-set due_date 12 months from completed_date when completed_date changes
        if (!empty($data['completed_date']) && empty($data['due_date'])) {
            $data['due_date'] = \Carbon\Carbon::parse($data['completed_date'])->addYear()->toDateString();
        }
        if (!empty($data['completed_date'])) {
            $data['status'] = 'signed';
        }

        $renewal->update($data);

        return redirect()->route('renewals.index')->with('success', 'Engagement letter updated.');
    }

    public function destroy(Renewal $renewal)
    {
        abort_unless(auth()->user()->isManager(), 403);
        $renewal->delete();

        return redirect()->route('renewals.index')->with('success', 'Engagement letter deleted.');
    }
}
