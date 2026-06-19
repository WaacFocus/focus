<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureManager
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->isManager()) {
            abort(403, 'This area requires manager access.');
        }

        return $next($request);
    }
}
