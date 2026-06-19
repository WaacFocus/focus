<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = UserActivityLog::with('user')
            ->orderByDesc('created_at');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(50)->withQueryString();

        $users = User::orderBy('name')->get();

        $today = now()->toDateString();

        $stats = [
            'logins_today'   => UserActivityLog::where('event', 'login')->whereDate('created_at', $today)->count(),
            'active_sessions'=> UserActivityLog::whereDate('created_at', $today)->distinct('session_id')->count('session_id'),
            'users_today'    => UserActivityLog::whereDate('created_at', $today)->distinct('user_id')->count('user_id'),
            'total_today'    => UserActivityLog::whereDate('created_at', $today)->count(),
        ];

        return view('activity.index', compact('logs', 'users', 'stats'));
    }
}
