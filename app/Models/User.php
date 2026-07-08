<?php

namespace App\Models;

use App\Services\Smtp2goService;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laragear\WebAuthn\WebAuthnAuthentication;

#[Fillable(['name', 'email', 'password', 'role', 'preferences', 'two_factor_secret', 'two_factor_confirmed_at'])]
#[Hidden(['password', 'remember_token', 'two_factor_secret'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, WebAuthnAuthentication;

    protected function casts(): array
    {
        return [
            'email_verified_at'       => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
            'password'                => 'hashed',
            'preferences'             => 'array',
        ];
    }

    public function sendPasswordResetNotification($token): void
    {
        $url = route('password.reset', ['token' => $token, 'email' => $this->email]);

        $html = '
            <p>Hi ' . e($this->name) . ',</p>
            <p>You requested a password reset for your Focus account. Click the button below to set a new password. This link expires in 60 minutes.</p>
            <p style="margin:24px 0;">
                <a href="' . $url . '" style="background:#17B4A7;color:#fff;padding:12px 24px;border-radius:6px;text-decoration:none;font-weight:600;">Reset Password</a>
            </p>
            <p>If you did not request this, you can safely ignore this email.</p>
            <p style="color:#999;font-size:12px;">Or copy this link into your browser:<br>' . $url . '</p>
        ';

        app(Smtp2goService::class)->send($this->email, $this->name, 'Reset your Focus password', $html);
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function hasTotpEnabled(): bool
    {
        return $this->two_factor_secret !== null && $this->two_factor_confirmed_at !== null;
    }

    public function hasPasskeys(): bool
    {
        return $this->webAuthnCredentials()->whereEnabled()->exists();
    }

    public function hasTwoFactorEnabled(): bool
    {
        return $this->hasTotpEnabled() || $this->hasPasskeys();
    }
}
