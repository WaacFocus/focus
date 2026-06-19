<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ImpersonateController extends Controller
{
    public function start(User $user)
    {
        abort_unless(auth()->user()->isManager(), 403);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot impersonate yourself.');
        }

        if (session()->has('impersonator_id')) {
            return back()->with('error', 'Stop the current impersonation session before starting a new one.');
        }

        session(['impersonator_id' => auth()->id(), 'impersonator_name' => auth()->user()->name]);

        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', 'Now viewing as ' . $user->name . '. Use the banner to return to your account.');
    }

    public function stop()
    {
        $impersonatorId = session('impersonator_id');

        if (!$impersonatorId) {
            return redirect()->route('dashboard');
        }

        $name = auth()->user()->name;

        session()->forget(['impersonator_id', 'impersonator_name']);

        Auth::loginUsingId($impersonatorId);

        return redirect()->route('users.index')
            ->with('success', 'Returned to your account. (Was viewing as ' . $name . ')');
    }
}
