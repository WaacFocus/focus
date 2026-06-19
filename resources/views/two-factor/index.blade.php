@extends('layouts.app')

@section('title', 'Security')
@section('page-title', 'Security')

@section('content')
<div class="mb-4">
    <h4 class="mb-1">Security</h4>
    <p class="text-muted small mb-0">Manage two-factor authentication methods for your account.</p>
</div>

<div class="row g-4">

    {{-- Authenticator App (TOTP) --}}
    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-start gap-3 mb-3">
                    <div class="rounded-3 p-3 flex-shrink-0" style="background:rgba(23,180,167,.12);">
                        <i class="bi bi-phone fs-4" style="color:var(--brand-teal);"></i>
                    </div>
                    <div>
                        <h6 class="fw-semibold mb-1">Authenticator App</h6>
                        <p class="text-muted small mb-0">Use Google Authenticator, Authy, or any TOTP app to generate login codes.</p>
                    </div>
                </div>

                @if($user->hasTotpEnabled())
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge rounded-pill text-bg-success"><i class="bi bi-check-circle me-1"></i>Enabled</span>
                        <span class="text-muted small">Since {{ $user->two_factor_confirmed_at->format('d M Y') }}</span>
                    </div>
                    <form method="POST" action="{{ route('two-factor.totp.disable') }}"
                          onsubmit="return confirm('Remove your authenticator app? You will need another 2FA method to stay protected.')">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-trash me-1"></i>Remove
                        </button>
                    </form>
                @else
                    <span class="badge rounded-pill text-bg-secondary mb-3"><i class="bi bi-dash-circle me-1"></i>Not enabled</span>
                    <div class="d-block">
                        <form method="POST" action="{{ route('two-factor.totp.enable') }}">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="bi bi-plus-lg me-1"></i>Set Up Authenticator App
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Passkeys --}}
    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-start gap-3 mb-3">
                    <div class="rounded-3 p-3 flex-shrink-0" style="background:rgba(26,157,217,.12);">
                        <i class="bi bi-fingerprint fs-4" style="color:var(--brand-blue);"></i>
                    </div>
                    <div>
                        <h6 class="fw-semibold mb-1">Passkeys</h6>
                        <p class="text-muted small mb-0">Use your device's fingerprint, face ID, or security key as a second factor.</p>
                    </div>
                </div>

                @if($credentials->isEmpty())
                    <span class="badge rounded-pill text-bg-secondary mb-3"><i class="bi bi-dash-circle me-1"></i>No passkeys registered</span>
                @else
                    <span class="badge rounded-pill text-bg-success mb-3"><i class="bi bi-check-circle me-1"></i>{{ $credentials->count() }} {{ Str::plural('passkey', $credentials->count()) }} registered</span>
                    <ul class="list-group list-group-flush mb-3">
                        @foreach($credentials as $cred)
                        <li class="list-group-item px-0 d-flex align-items-center justify-content-between">
                            <div>
                                <i class="bi bi-key me-2 text-muted"></i>
                                <span class="small fw-medium">{{ $cred->nickname ?: 'Passkey' }}</span>
                                <span class="text-muted small ms-2">Added {{ $cred->created_at->format('d M Y') }}</span>
                            </div>
                            <form method="POST" action="{{ route('two-factor.passkey.delete', $cred->id) }}"
                                  onsubmit="return confirm('Remove this passkey?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </li>
                        @endforeach
                    </ul>
                @endif

                <button type="button" id="registerPasskeyBtn" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>Add Passkey
                </button>
                <div id="passkey-register-error" class="alert alert-danger py-2 small mt-2" style="display:none;"></div>
                <div id="passkey-register-success" class="alert alert-success py-2 small mt-2" style="display:none;">
                    <i class="bi bi-check-circle me-1"></i>Passkey registered! Refreshing…
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

function base64UrlDecode(input) {
    input = input.replace(/-/g, '+').replace(/_/g, '/');
    const pad = input.length % 4;
    if (pad) input += '='.repeat(4 - pad);
    return Uint8Array.from(atob(input), c => c.charCodeAt(0));
}

function arrayToBase64(buffer) {
    return btoa(String.fromCharCode(...new Uint8Array(buffer)));
}

document.getElementById('registerPasskeyBtn')?.addEventListener('click', async () => {
    const btn = document.getElementById('registerPasskeyBtn');
    const errorEl = document.getElementById('passkey-register-error');
    const successEl = document.getElementById('passkey-register-success');
    errorEl.style.display = 'none';
    successEl.style.display = 'none';
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Waiting…';

    try {
        const optRes = await fetch('{{ route('webauthn.register.options') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({})
        });

        if (!optRes.ok) throw new Error('Could not start passkey registration.');
        const options = await optRes.json();

        options.challenge = base64UrlDecode(options.challenge);
        options.user.id   = base64UrlDecode(options.user.id);
        if (options.excludeCredentials) {
            options.excludeCredentials = options.excludeCredentials.map(c => ({ ...c, id: base64UrlDecode(c.id) }));
        }

        const credential = await navigator.credentials.create({ publicKey: options });

        const nickname = prompt('Give this passkey a name (optional):', navigator.platform || 'My Device');

        const payload = {
            id:    credential.id,
            type:  credential.type,
            rawId: arrayToBase64(credential.rawId),
            authenticatorAttachment: credential.authenticatorAttachment,
            clientExtensionResults:  credential.getClientExtensionResults(),
            nickname: nickname || null,
            response: {
                clientDataJSON:  arrayToBase64(credential.response.clientDataJSON),
                attestationObject: arrayToBase64(credential.response.attestationObject),
            }
        };

        const regRes = await fetch('{{ route('webauthn.register') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        });

        if (!regRes.ok) {
            const err = await regRes.json().catch(() => ({}));
            throw new Error(err.message || 'Registration failed.');
        }

        successEl.style.display = 'block';
        setTimeout(() => window.location.reload(), 1200);

    } catch (err) {
        if (err.name === 'NotAllowedError') {
            errorEl.textContent = 'Registration was cancelled.';
        } else {
            errorEl.textContent = err.message || 'An error occurred.';
        }
        errorEl.style.display = 'block';
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-plus-lg me-1"></i>Add Passkey';
    }
});
</script>
@endpush
