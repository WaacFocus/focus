<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Job;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $query = Job::with(['client', 'assignedTo']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%$s%")
                  ->orWhereHas('client', fn($c) => $c->where('company_name', 'like', "%$s%"));
            });
        }
        if ($request->status === 'all') {
            // show every status
        } elseif ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->whereIn('status', ['pending', 'in_progress']);
        }
        if ($request->assigned_to === 'all') {
            // show all users
        } elseif ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        } else {
            $query->where('assigned_to', Auth::id());
        }
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }
        if ($request->filled('days') && in_array($request->days, ['30', '60', '90'])) {
            $query->whereBetween('due_date', [now()->startOfDay(), now()->addDays((int) $request->days)->endOfDay()]);
        }

        $jobs    = $query->orderByRaw("CASE status WHEN 'pending' THEN 0 WHEN 'in_progress' THEN 1 ELSE 2 END")
                         ->orderBy('due_date')
                         ->paginate(25)
                         ->withQueryString();

        $clients = Client::orderBy('company_name')->get(['id', 'company_name']);
        $users   = User::orderBy('name')->get(['id', 'name']);

        return view('jobs.index', compact('jobs', 'clients', 'users'));
    }

    public function show(Request $request, Job $job)
    {
        if ($request->expectsJson()) {
            return response()->json($job);
        }

        return redirect()->route('jobs.index');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'client_id'   => 'nullable|exists:clients,id',
            'assigned_to' => 'required|exists:users,id',
            'frequency'   => 'required|in:weekly,monthly,quarterly,yearly,one-off',
            'due_date'    => 'required|date',
            'status'      => 'required|in:pending,in_progress,completed',
            'notes'       => 'nullable|string',
        ]);

        $job = Job::create($data);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Job created successfully.', 'id' => $job->id]);
        }

        return redirect()->route('jobs.index')->with('success', 'Job created.');
    }

    public function update(Request $request, Job $job)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'client_id'   => 'nullable|exists:clients,id',
            'assigned_to' => 'required|exists:users,id',
            'frequency'   => 'required|in:weekly,monthly,quarterly,yearly,one-off',
            'due_date'    => 'required|date',
            'status'      => 'required|in:pending,in_progress,completed',
            'notes'       => 'nullable|string',
        ]);

        $job->update($data);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Job updated successfully.', 'id' => $job->id]);
        }

        return redirect()->route('jobs.index')->with('success', 'Job updated.');
    }

    public function updateStatus(Request $request, Job $job)
    {
        $request->validate(['status' => 'required|in:pending,in_progress,completed']);

        $data = ['status' => $request->status];

        if ($request->status === 'completed') {
            $data['completed_at'] = now();
            $job->update($data);
            $next = $job->scheduleNext();
            return response()->json([
                'message'  => 'Status updated.',
                'next_due' => $next?->due_date->format('d M Y'),
            ]);
        }

        $job->update($data);
        return response()->json(['message' => 'Status updated.']);
    }

    public function complete(Request $request, Job $job)
    {
        $job->update([
            'status'       => 'completed',
            'completed_at' => now(),
        ]);

        $next = $job->scheduleNext();

        if ($request->expectsJson()) {
            return response()->json([
                'message'  => 'Job marked complete.',
                'next_due' => $next?->due_date->format('d M Y'),
                'next_id'  => $next?->id,
            ]);
        }

        return back()->with('success', 'Job completed' . ($next ? ' — next due ' . $next->due_date->format('d M Y') : '') . '.');
    }

    public function destroy(Job $job)
    {
        abort_unless(auth()->user()->isManager(), 403);
        $job->delete();

        return redirect()->route('jobs.index')->with('success', 'Job deleted.');
    }
}
