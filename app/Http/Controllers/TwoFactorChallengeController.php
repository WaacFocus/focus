<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserActivityLog;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Laragear\WebAuthn\Http\Requests\AssertedRequest;
use Laragear\WebAuthn\Http\Requests\AssertionRequest;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorChallengeController extends Controller
{
    public function show(Request $request)
    {
        if (!$request->session()->has('two_factor.user_id')) {
            return redirect()->route('login');
        }

        $user = User::findOrFail($request->session()->get('two_factor.user_id'));

        return view('auth.two-factor-challenge', [
            'hasTotpEnabled' => $user->hasTotpEnabled(),
            'hasPasskeys'    => $user->hasPasskeys(),
        ]);
    }

    public function verifyTotp(Request $request)
    {
        $request->validate(['code' => ['required', 'string']]);

        $userId = $request->session()->get('two_factor.user_id');
        $user   = $userId ? User::find($userId) : null;

        if (!$user || !$user->hasTotpEnabled()) {
            return redirect()->route('login');
        }

        $google2fa = new Google2FA();
        $valid     = $google2fa->verifyKey($user->two_factor_secret, $request->code);

        if (!$valid) {
            return back()->withErrors(['code' => 'The code is incorrect or has expired.']);
        }

        $this->completeLogin($request, $user);

        return redirect()->intended(route('dashboard'));
    }

    public function passkeyOptions(AssertionRequest $request): Responsable
    {
        $userId = $request->session()->get('two_factor.user_id');
        $user   = $userId ? User::find($userId) : null;

        if (!$user) {
            abort(403);
        }

        return $request->toVerify(['email' => $user->email]);
    }

    public function verifyPasskey(AssertedRequest $request): Response
    {
        $pendingUserId = $request->session()->get('two_factor.user_id');

        if (!$pendingUserId) {
            return response()->json(['message' => 'No pending 2FA session.'], 422);
        }

        $remember = $request->session()->get('two_factor.remember', false);

        if (!$request->login($remember)) {
            return response()->json(['message' => 'Passkey verification failed.'], 422);
        }

        // Ensure the passkey belonged to the pending user
        if (Auth::id() !== (int) $pendingUserId) {
            Auth::logout();
            return response()->json(['message' => 'Passkey does not match the account.'], 422);
        }

        $this->completeLogin($request, Auth::user());

        return response()->noContent(204);
    }

    private function completeLogin(Request $request, User $user): void
    {
        $remember = $request->session()->pull('two_factor.remember', false);
        $request->session()->forget('two_factor');

        if (!Auth::check()) {
            Auth::login($user, $remember);
        }

        $request->session()->regenerate();

        UserActivityLog::create([
            'user_id'    => $user->id,
            'event'      => 'login',
            'description'=> 'Signed in (2FA verified)',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'session_id' => session()->getId(),
        ]);
    }
}
