@extends('layouts.app')

@section('title', 'Set Up Authenticator App')
@section('page-title', 'Security')

@section('content')
<div class="mb-4">
    <a href="{{ route('two-factor.index') }}" class="text-muted text-decoration-none small">
        <i class="bi bi-arrow-left me-1"></i>Security
    </a>
    <h4 class="mb-0 mt-1">Set Up Authenticator App</h4>
</div>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">

                <div class="text-center mb-4">
                    <p class="text-muted small mb-3">Scan this QR code with your authenticator app (Google Authenticator, Authy, etc.)</p>
                    <div class="d-inline-block p-3 bg-white border rounded-3">
                        {!! $qrSvg !!}
                    </div>
                </div>

                <div class="mb-4">
                    <p class="text-muted small text-center mb-2">Or enter the code manually:</p>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control font-monospace text-center" value="{{ $secret }}" id="secretInput" readonly>
                        <button class="btn btn-outline-secondary" type="button" onclick="navigator.clipboard.writeText('{{ $secret }}');this.textContent='Copied!'">
                            Copy
                        </button>
                    </div>
                </div>

                <hr>

                <p class="text-muted small mb-3">Once scanned, enter the 6-digit code from your app to confirm setup:</p>

                <form method="POST" action="{{ route('two-factor.totp.confirm') }}">
                    @csrf
                    <div class="mb-3">
                        <input type="text" name="code" id="code"
                               class="form-control form-control-lg text-center font-monospace @error('code') is-invalid @enderror"
                               placeholder="000 000"
                               maxlength="6"
                               inputmode="numeric"
                               autocomplete="one-time-code"
                               autofocus>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check2 me-1"></i>Confirm & Enable
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
