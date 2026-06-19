<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Job;
use App\Models\Project;
use App\Models\Renewal;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'clients'         => Client::where('status', 'active')->count(),
            'active_projects' => Project::where('status', 'active')->count(),
            'pending_tasks'   => Task::whereIn('status', ['pending', 'in_progress'])->count(),
            'upcoming_renewals' => Renewal::where('status', 'pending')
                ->where('renewal_date', '<=', now()->addDays(30))
                ->count(),
        ];

        $upcoming_renewals = Renewal::with('client', 'service')
            ->where('status', 'pending')
            ->where('renewal_date', '<=', now()->addDays(60))
            ->orderBy('renewal_date')
            ->take(5)
            ->get();

        $recent_tasks = Task::with('project.client')
            ->whereIn('status', ['pending', 'in_progress'])
            ->orderByRaw('is_urgent DESC')
            ->orderBy('due_date')
            ->take(5)
            ->get();

        $recent_projects = Project::with('client')
            ->where('status', 'active')
            ->latest()
            ->take(5)
            ->get();

        $my_jobs = Job::with('client')
            ->where('assigned_to', Auth::id())
            ->whereIn('status', ['pending', 'in_progress'])
            ->orderBy('due_date')
            ->get();

        return view('dashboard.index', compact('stats', 'upcoming_renewals', 'recent_tasks', 'recent_projects', 'my_jobs'));
    }
}
