<?php

namespace App\Http\Middleware;

use App\Models\UserActivityLog;
use Closure;
use Illuminate\Http\Request;

class LogUserActivity
{
    private const SKIP_PREFIXES = ['api/', '_debugbar', 'livewire'];

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (!auth()->check() || $request->method() !== 'GET') {
            return $response;
        }

        $path = ltrim($request->path(), '/');

        foreach (self::SKIP_PREFIXES as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return $response;
            }
        }

        $status = $response->getStatusCode();
        if ($status >= 400) {
            return $response;
        }

        UserActivityLog::create([
            'user_id'    => auth()->id(),
            'event'      => 'page_view',
            'description'=> $this->describeUrl($request),
            'url'        => $request->fullUrl(),
            'method'     => 'GET',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'session_id' => session()->getId(),
        ]);

        return $response;
    }

    private function describeUrl(Request $request): string
    {
        $named = $request->route()?->getName();
        if (!$named) {
            return 'Visited ' . $request->path();
        }

        $labels = [
            'dashboard'          => 'Viewed Dashboard',
            'clients.index'      => 'Viewed Clients list',
            'clients.show'       => 'Viewed Client',
            'clients.create'     => 'Opened new Client form',
            'clients.edit'       => 'Opened edit Client form',
            'projects.index'     => 'Viewed Projects list',
            'projects.show'      => 'Viewed Project',
            'projects.create'    => 'Opened new Project form',
            'projects.edit'      => 'Opened edit Project form',
            'tasks.index'        => 'Viewed Tasks list',
            'tasks.show'         => 'Viewed Task',
            'tasks.create'       => 'Opened new Task form',
            'tasks.edit'         => 'Opened edit Task form',
            'services.index'     => 'Viewed Services list',
            'products.index'     => 'Viewed Products list',
            'renewals.index'     => 'Viewed Renewals list',
            'jobs.index'         => 'Viewed Jobs list',
            'reports.index'      => 'Viewed Reports',
            'users.index'        => 'Viewed Users list',
            'client-types.index' => 'Viewed Client Types list',
            'activity.index'     => 'Viewed Activity log',
        ];

        return $labels[$named] ?? 'Visited ' . $request->path();
    }
}
