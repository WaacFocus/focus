<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Engagement Letter — Sign</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f7f6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
        .letter-header { background: #0C3D38; color: #fff; padding: 1.5rem 2rem; border-radius: .5rem .5rem 0 0; }
        .letter-body { background: #fff; padding: 2rem; font-family: Georgia, serif; line-height: 1.8; font-size: 15px; }
        .letter-section h3 { font-size: 15px; color: #0C3D38; margin-top: 1.5rem; margin-bottom: .3rem; font-family: -apple-system, sans-serif; font-weight: 600; }
        .sign-box { background: #fff; border: 2px solid #0C3D38; border-radius: .5rem; padding: 1.5rem; }
    </style>
</head>
<body>
<div class="container py-5" style="max-width:780px;">

    <div class="card shadow-sm overflow-hidden mb-4">
        <div class="letter-header">
            <div class="small opacity-75 text-uppercase" style="letter-spacing:.06em;">Engagement Letter</div>
            <h1 class="h4 mb-0 mt-1">{{ $letter->client->company_name }}</h1>
            <div class="small opacity-75 mt-1">Please read carefully and sign below</div>
        </div>
        <div class="letter-body">
            <p><strong>Dear {{ $letter->client->contact_name ?: $letter->client->company_name }},</strong></p>
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
        <h5 class="fw-semibold mb-3" style="color:#0C3D38;"><i class="bi bi-pen me-2"></i>Sign this Engagement Letter</h5>
        <form method="POST" action="{{ route('sign.sign', $token) }}">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Your Full Name <span class="text-danger">*</span></label>
                <input type="text" name="signed_name" class="form-control form-control-lg"
                       placeholder="Enter your full name" value="{{ old('signed_name') }}" required autocomplete="name">
                <div class="form-text">Please enter your full legal name as it should appear on the signed letter.</div>
            </div>
            <div class="mb-4">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" name="agreed" id="agreedCheck" value="1" required>
                    <label class="form-check-label" for="agreedCheck">
                        I confirm that I have read and understood the terms of this engagement letter and agree to be bound by them.
                    </label>
                </div>
            </div>
            <button type="submit" class="btn btn-lg w-100" style="background:#0C3D38;color:#fff;">
                <i class="bi bi-check-circle me-2"></i>Sign Engagement Letter
            </button>
        </form>
    </div>

    <p class="text-center text-muted small">
        Your IP address (<strong>{{ request()->ip() }}</strong>) and the time of signing will be recorded.<br>
        A copy of the signed letter will be emailed to you.
    </p>
</div>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</body>
</html>
