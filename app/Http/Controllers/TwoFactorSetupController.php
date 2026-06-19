<?php

namespace App\Http\Controllers;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorSetupController extends Controller
{
    public function show()
    {
        $user        = Auth::user();
        $credentials = $user->webAuthnCredentials()->whereEnabled()->get();

        return view('two-factor.index', compact('user', 'credentials'));
    }

    public function enableTotp(Request $request)
    {
        $google2fa = new Google2FA();
        $secret    = $google2fa->generateSecretKey();

        $request->session()->put('two_factor_totp_secret', $secret);

        $otpauthUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            Auth::user()->email,
            $secret
        );

        $qrSvg = $this->generateQrSvg($otpauthUrl);

        return view('two-factor.totp-setup', compact('secret', 'qrSvg'));
    }

    public function confirmTotp(Request $request)
    {
        $request->validate(['code' => ['required', 'string', 'digits:6']]);

        $secret = $request->session()->get('two_factor_totp_secret');

        if (!$secret) {
            return redirect()->route('two-factor.index')->with('error', 'Setup session expired. Please try again.');
        }

        $google2fa = new Google2FA();

        if (!$google2fa->verifyKey($secret, $request->code)) {
            return back()->withErrors(['code' => 'Incorrect code. Please try again.']);
        }

        $user = Auth::user();
        $user->update([
            'two_factor_secret'       => $secret,
            'two_factor_confirmed_at' => now(),
        ]);

        $request->session()->forget('two_factor_totp_secret');

        return redirect()->route('two-factor.index')->with('success', 'Authenticator app enabled.');
    }

    public function disableTotp()
    {
        Auth::user()->update([
            'two_factor_secret'       => null,
            'two_factor_confirmed_at' => null,
        ]);

        return redirect()->route('two-factor.index')->with('success', 'Authenticator app removed.');
    }

    public function deletePasskey(Request $request, string $id)
    {
        $credential = Auth::user()
            ->webAuthnCredentials()
            ->where('id', $id)
            ->firstOrFail();

        $credential->delete();

        return redirect()->route('two-factor.index')->with('success', 'Passkey removed.');
    }

    private function generateQrSvg(string $content): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);

        return $writer->writeString($content);
    }
}
