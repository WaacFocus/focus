<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Already Signed — Engagement Letter</title>
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16.png">
    <link rel="shortcut icon" href="/favicon-32.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: #f0fafa; font-family: Arial, sans-serif; }
        .brand-header { background: #fff; border-bottom: 3px solid #3DBFB8; padding: 1rem 2rem; margin-bottom: 2.5rem; text-align: center; }
        .brand-header img { height: 42px; }
    </style>
</head>
<body>

<div class="brand-header">
    <img src="{{ asset('images/woods-logo.png') }}" alt="Woods Accounting & Consulting">
</div>

<div class="container pb-5" style="max-width:520px;">
    <div class="card shadow-sm text-center p-5">
        <div class="mb-4">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                 style="width:80px;height:80px;background:#3DBFB8;">
                <i class="bi bi-patch-check text-white" style="font-size:2.2rem;"></i>
            </div>
            <h2 class="fw-bold" style="color:#3DBFB8;">Already Signed</h2>
            <p class="text-muted">This engagement letter for <strong>{{ $letter->client->company_name }}</strong> has already been signed.</p>
        </div>
        <div class="rounded p-3 text-start mb-4" style="background:#f0fafa;border:1px solid #c8eeec;">
            <div class="small text-muted mb-1" style="text-transform:uppercase;letter-spacing:.05em;font-size:.7rem;">Signing Details</div>
            <table class="w-100" style="font-size:.9rem;">
                <tr>
                    <td class="text-muted py-1" style="width:110px;">Signed by</td>
                    <td class="fw-semibold">{{ $letter->signed_name }}</td>
                </tr>
                <tr>
                    <td class="text-muted py-1">Signed on</td>
                    <td>{{ $letter->signed_at->format('d F Y \a\t H:i') }}</td>
                </tr>
            </table>
        </div>
        <p class="text-muted small mb-0">If you believe this is an error, please contact us directly.</p>
    </div>
</div>

</body>
</html>
