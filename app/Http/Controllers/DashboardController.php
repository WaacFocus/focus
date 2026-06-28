<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Job;
use App\Models\Renewal;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'clients'           => Client::where('status', 'active')->count(),
            'pending_tasks'     => Task::whereIn('status', ['pending', 'in_progress'])->count(),
            'upcoming_renewals' => Renewal::whereIn('status', ['pending', 'sent'])
                ->where('due_date', '<=', now()->addDays(30))
                ->count(),
        ];

        $upcoming_renewals = Renewal::with('client')
            ->whereIn('status', ['pending', 'sent'])
            ->where('due_date', '<=', now()->addDays(60))
            ->orderBy('due_date')
            ->take(5)
            ->get();

        $recent_tasks = Task::whereIn('status', ['pending', 'in_progress'])
            ->orderByRaw('is_urgent DESC')
            ->orderBy('due_date')
            ->take(5)
            ->get();

        $jobsQuery = Job::with('client')
            ->where('assigned_to', Auth::id());

        if ($request->filled('jf_status')) {
            $jobsQuery->where('status', $request->jf_status);
        } else {
            $jobsQuery->whereIn('status', ['pending', 'in_progress']);
        }
        if ($request->filled('jf_frequency')) {
            $jobsQuery->where('frequency', $request->jf_frequency);
        }
        if ($request->filled('jf_client')) {
            $jobsQuery->where('client_id', $request->jf_client);
        }
        if ($request->filled('jf_from')) {
            $jobsQuery->where('due_date', '>=', $request->jf_from);
        }
        if ($request->filled('jf_to')) {
            $jobsQuery->where('due_date', '<=', $request->jf_to);
        }

        $my_jobs       = $jobsQuery->orderBy('due_date')->get();
        $my_job_clients = Client::whereHas('jobs', fn($q) => $q->where('assigned_to', Auth::id()))
            ->orderBy('company_name')->get(['id', 'company_name']);

        return view('dashboard.index', compact('stats', 'upcoming_renewals', 'recent_tasks', 'my_jobs', 'my_job_clients'));
    }
}
