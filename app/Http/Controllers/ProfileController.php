<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function password()
    {
        return view('profile.password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.current_password' => 'The current password you entered is incorrect.',
        ]);

        Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }

    public function savePreference(Request $request)
    {
        $request->validate([
            'key'   => ['required', 'string', 'max:64', 'regex:/^[a-z0-9_]+$/'],
            'value' => ['required'],
        ]);

        $user  = Auth::user();
        $prefs = $user->preferences ?? [];
        $prefs[$request->key] = $request->value;
        $user->update(['preferences' => $prefs]);

        return response()->json(['ok' => true]);
    }
}
