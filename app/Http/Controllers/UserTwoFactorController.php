<?php

namespace App\Http\Controllers;

use App\Models\User;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;

class UserTwoFactorController extends Controller
{
    public function __construct()
    {
        abort_unless(auth()->check() && auth()->user()->isManager(), 403);
    }

    public function generateTotp(User $user)
    {
        $google2fa = new Google2FA();
        $secret    = $google2fa->generateSecretKey();

        // Store pending secret in session keyed by user so multiple panels can be open
        session()->put("admin_2fa_secret_{$user->id}", $secret);

        $otpauthUrl = $google2fa->getQRCodeUrl(config('app.name'), $user->email, $secret);

        $renderer = new ImageRenderer(new RendererStyle(180), new SvgImageBackEnd());
        $qrSvg    = (new Writer($renderer))->writeString($otpauthUrl);

        return response()->json([
            'secret' => $secret,
            'qr_svg' => $qrSvg,
        ]);
    }

    public function confirmTotp(Request $request, User $user)
    {
        $request->validate(['code' => ['required', 'string', 'digits:6']]);

        $secret = session()->get("admin_2fa_secret_{$user->id}");

        if (!$secret) {
            return response()->json(['message' => 'Setup session expired. Please generate a new QR code.'], 422);
        }

        $google2fa = new Google2FA();

        if (!$google2fa->verifyKey($secret, $request->code)) {
            return response()->json(['errors' => ['code' => ['Incorrect code — please try again.']]], 422);
        }

        $user->update([
            'two_factor_secret'       => $secret,
            'two_factor_confirmed_at' => now(),
        ]);

        session()->forget("admin_2fa_secret_{$user->id}");

        return response()->json(['message' => 'Authenticator app enabled for ' . $user->name . '.']);
    }

    public function disableTotp(User $user)
    {
        $user->update([
            'two_factor_secret'       => null,
            'two_factor_confirmed_at' => null,
        ]);

        return response()->json(['message' => 'Authenticator app removed.']);
    }

    public function deletePasskey(User $user, string $id)
    {
        $user->webAuthnCredentials()->where('id', $id)->firstOrFail()->delete();

        return response()->json(['message' => 'Passkey removed.']);
    }

    public function reset(User $user)
    {
        $user->update([
            'two_factor_secret'       => null,
            'two_factor_confirmed_at' => null,
        ]);

        $user->webAuthnCredentials()->delete();

        return response()->json(['message' => 'All two-factor methods removed.']);
    }
}
