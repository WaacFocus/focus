<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Engagement Letter — {{ $letter->client->company_name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: #f0fafa; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
        .brand-header { background: #fff; border-bottom: 3px solid #3DBFB8; padding: 1rem 2rem; margin-bottom: 2rem; }
        .brand-header img { height: 42px; }
        .letter-meta { background: #3DBFB8; color: #fff; padding: 1.25rem 2rem; border-radius: .5rem .5rem 0 0; }
        .letter-meta .label { font-size: .7rem; letter-spacing: .08em; text-transform: uppercase; opacity: .75; }
        .letter-body { background: #fff; padding: 2rem; font-family: Georgia, serif; line-height: 1.85; font-size: 15px; border-radius: 0 0 .5rem .5rem; }
        .letter-section h3 { font-size: 14px; color: #3DBFB8; margin-top: 1.5rem; margin-bottom: .3rem; font-family: -apple-system, sans-serif; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; }
        .sign-box { background: #fff; border: 2px solid #3DBFB8; border-radius: .5rem; padding: 1.75rem; }
        .sign-box h5 { color: #3DBFB8; }
        .btn-sign { background: #3DBFB8; border: none; color: #fff; font-weight: 600; }
        .btn-sign:hover { background: #30a8a2; color: #fff; }
        .ip-note { color: #888; font-size: .8rem; }
    </style>
</head>
<body>

<div class="brand-header d-flex align-items-center justify-content-between">
    <img src="{{ asset('images/woods-logo.png') }}" alt="Woods Accounting & Consulting">
    <span class="text-muted small">Engagement Letter</span>
</div>

<div class="container pb-5" style="max-width:780px;">

    <div class="shadow-sm overflow-hidden mb-4" style="border-radius:.5rem;">
        <div class="letter-meta">
            <div class="label">Engagement Letter</div>
            <h1 class="h4 mb-0 mt-1 fw-bold">{{ $letter->client->company_name }}</h1>
            <div class="mt-1" style="font-size:.85rem;opacity:.8;">Please read carefully and sign below</div>
        </div>
        <div class="letter-body">
            <p><strong>Dear {{ $letter->client->contact_first_name_greeting }},</strong></p>
            @foreach($letter->sections as $section)
            <div class="letter-section">
                <h3>{{ $section['title'] }}</h3>
                <p style="white-space:pre-line;margin-bottom:.5rem;">{{ $section['body'] }}</p>
            </div>
            @endforeach
        </div>
    </div>

    @if($errors->any())
    <div class="alert alert-danger mb-4">
        @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
    </div>
    @endif

    <div class="sign-box mb-4">
        <h5 class="fw-semibold mb-1"><i class="bi bi-pen me-2"></i>Sign this Engagement Letter</h5>
        <p class="text-muted small mb-3">Please enter your full legal name and confirm your agreement below.</p>
        <form method="POST" action="{{ route('sign.sign', $token) }}">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Your Full Name <span class="text-danger">*</span></label>
                <input type="text" name="signed_name" class="form-control form-control-lg"
                       placeholder="Enter your full name" value="{{ old('signed_name') }}" required autocomplete="name">
            </div>
            <div class="mb-4">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" name="agreed" id="agreedCheck" value="1" required>
                    <label class="form-check-label" for="agreedCheck">
                        I confirm that I have read and understood the terms of this engagement letter and agree to be bound by them.
                    </label>
                </div>
            </div>
            <button type="submit" class="btn btn-sign btn-lg w-100">
                <i class="bi bi-check-circle me-2"></i>Sign Engagement Letter
            </button>
        </form>
    </div>

    <p class="text-center ip-note">
        Your IP address (<strong>{{ request()->ip() }}</strong>) and the time of signing will be recorded.<br>
        A copy of the signed letter will be emailed to you.
    </p>

</div>
</body>
</html>
