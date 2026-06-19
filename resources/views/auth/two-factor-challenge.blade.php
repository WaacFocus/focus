<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Two-Factor Authentication — Focus</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root { --brand-dark: #0C3D38; --brand-teal: #17B4A7; }
        body { background: #f0f4f4; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { border: none; border-radius: .875rem; box-shadow: 0 4px 24px rgba(0,0,0,.08); }
        .brand-header { background: var(--brand-dark); border-radius: .875rem .875rem 0 0; padding: 1.5rem; text-align: center; }
        .btn-primary { background: var(--brand-teal); border-color: var(--brand-teal); }
        .btn-primary:hover { background: #0ea397; border-color: #0ea397; }
        .nav-tabs .nav-link.active { color: var(--brand-teal); border-bottom-color: var(--brand-teal); font-weight: 600; }
        .nav-tabs .nav-link { color: #6c757d; }
        .passkey-btn { border: 2px solid var(--brand-teal); color: var(--brand-teal); background: white; transition: all .15s; }
        .passkey-btn:hover { background: var(--brand-teal); color: white; }
        #passkey-error, #passkey-success { display: none; }
    </style>
</head>
<body>
<div style="width: 100%; max-width: 420px; padding: 1rem;">
    <div class="card">
        <div class="brand-header">
            <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
                <i class="bi bi-shield-lock-fill fs-4 text-white"></i>
                <span class="fw-bold fs-5 text-white">Two-Factor Authentication</span>
            </div>
            <p class="mb-0 text-white-50 small">Verify your identity to continue</p>
        </div>

        <div class="card-body p-4">
            @if($errors->any())
                <div class="alert alert-danger py-2 small">
                    <i class="bi bi-exclamation-circle me-1"></i>{{ $errors->first() }}
                </div>
            @endif

            {{-- Tabs --}}
            <ul class="nav nav-tabs mb-4" id="2faTabs">
                @if($hasPasskeys)
                <li class="nav-item">
                    <button class="nav-link {{ $hasPasskeys ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#passkeyTab">
                        <i class="bi bi-fingerprint me-1"></i>Passkey
                    </button>
                </li>
                @endif
                @if($hasTotpEnabled)
                <li class="nav-item">
                    <button class="nav-link {{ !$hasPasskeys ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#totpTab">
                        <i class="bi bi-phone me-1"></i>Authenticator
                    </button>
                </li>
                @endif
            </ul>

            <div class="tab-content">
                {{-- Passkey tab --}}
                @if($hasPasskeys)
                <div class="tab-pane fade show active" id="passkeyTab">
                    <p class="text-muted small mb-4 text-center">Use your device's fingerprint, face, or security key to verify.</p>

                    <div class="d-grid mb-3">
                        <button type="button" id="passkeyBtn" class="btn passkey-btn btn-lg py-3">
                            <i class="bi bi-fingerprint me-2 fs-5"></i>Verify with Passkey
                        </button>
                    </div>

                    <div id="passkey-error" class="alert alert-danger py-2 small"></div>
                    <div id="passkey-success" class="alert alert-success py-2 small">
                        <i class="bi bi-check-circle me-1"></i>Verified! Redirecting…
                    </div>
                </div>
                @endif

                {{-- TOTP tab --}}
                @if($hasTotpEnabled)
                <div class="tab-pane fade {{ !$hasPasskeys ? 'show active' : '' }}" id="totpTab">
                    <p class="text-muted small mb-3 text-center">Enter the 6-digit code from your authenticator app.</p>

                    <form method="POST" action="{{ route('two-factor.totp') }}">
                        @csrf
                        <div class="mb-3">
                            <input type="text" name="code" id="code"
                                   class="form-control form-control-lg text-center font-monospace @error('code') is-invalid @enderror"
                                   placeholder="000 000"
                                   maxlength="6"
                                   autocomplete="one-time-code"
                                   inputmode="numeric"
                                   autofocus>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check2 me-1"></i>Verify Code
                            </button>
                        </div>
                    </form>
                </div>
                @endif
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('login') }}" class="text-muted small text-decoration-none">
                    <i class="bi bi-arrow-left me-1"></i>Back to login
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@if($hasPasskeys)
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

document.getElementById('passkeyBtn').addEventListener('click', async () => {
    const btn = document.getElementById('passkeyBtn');
    const errorEl = document.getElementById('passkey-error');
    const successEl = document.getElementById('passkey-success');

    errorEl.style.display = 'none';
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Waiting for device…';

    try {
        // Get assertion options
        const optRes = await fetch('{{ route('two-factor.passkey.options') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({})
        });

        if (!optRes.ok) throw new Error('Failed to get passkey options.');

        const options = await optRes.json();
        options.challenge = base64UrlDecode(options.challenge);

        if (options.allowCredentials) {
            options.allowCredentials = options.allowCredentials.map(c => ({
                ...c, id: base64UrlDecode(c.id)
            }));
        }

        const credential = await navigator.credentials.get({ publicKey: options });

        const payload = {
            id:    credential.id,
            type:  credential.type,
            rawId: arrayToBase64(credential.rawId),
            authenticatorAttachment: credential.authenticatorAttachment,
            clientExtensionResults:  credential.getClientExtensionResults(),
            response: {
                clientDataJSON:    arrayToBase64(credential.response.clientDataJSON),
                authenticatorData: arrayToBase64(credential.response.authenticatorData),
                signature:         arrayToBase64(credential.response.signature),
                userHandle:        credential.response.userHandle ? arrayToBase64(credential.response.userHandle) : null,
            }
        };

        const verifyRes = await fetch('{{ route('two-factor.passkey.verify') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        });

        if (!verifyRes.ok) {
            const err = await verifyRes.json().catch(() => ({}));
            throw new Error(err.message || 'Passkey verification failed.');
        }

        successEl.style.display = 'block';
        setTimeout(() => window.location.href = '/', 800);

    } catch (err) {
        if (err.name === 'NotAllowedError') {
            errorEl.textContent = 'Passkey prompt was cancelled.';
        } else {
            errorEl.textContent = err.message || 'An error occurred. Please try again.';
        }
        errorEl.style.display = 'block';
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-fingerprint me-2 fs-5"></i>Verify with Passkey';
    }
});
</script>
@endif
</body>
</html>
