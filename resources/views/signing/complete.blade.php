<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Engagement Letter Signed</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>body { background: #f4f7f6; }</style>
</head>
<body>
<div class="container py-5" style="max-width:560px;">
    <div class="card shadow-sm text-center p-5">
        <div class="mb-4">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                 style="width:80px;height:80px;background:#0C3D38;">
                <i class="bi bi-check-lg text-white" style="font-size:2.2rem;"></i>
            </div>
            <h2 class="fw-bold">Letter Signed</h2>
            <p class="text-muted">Thank you, <strong>{{ $letter->signed_name }}</strong>. Your engagement letter has been signed successfully.</p>
        </div>
        <div class="bg-light rounded p-3 text-start mb-4">
            <div class="small text-muted mb-1">Signed on</div>
            <div class="fw-semibold">{{ $letter->signed_at->format('d F Y \a\t H:i') }}</div>
            <div class="small text-muted mt-2 mb-1">IP Address</div>
            <code>{{ $letter->signed_ip }}</code>
        </div>
        <p class="text-muted small">A copy of the signed engagement letter has been emailed to you for your records.</p>
    </div>
</div>
</body>
</html>
