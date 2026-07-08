<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password — Focus</title>
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16.png">
    <link rel="shortcut icon" href="/favicon-32.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            min-height: 100vh;
            background: #0C3D38;
            background-image: radial-gradient(ellipse at 20% 50%, rgba(23,180,167,.25) 0%, transparent 60%),
                              radial-gradient(ellipse at 80% 20%, rgba(247,148,29,.15) 0%, transparent 50%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            background: #fff;
            border-radius: 1rem;
            padding: 2.5rem;
            box-shadow: 0 24px 64px rgba(0,0,0,.35);
        }
        .btn-brand {
            background: #17B4A7;
            border-color: #17B4A7;
            color: #fff;
        }
        .btn-brand:hover {
            background: #0ea397;
            border-color: #0ea397;
            color: #fff;
        }
        .form-control:focus {
            border-color: #17B4A7;
            box-shadow: 0 0 0 .25rem rgba(23,180,167,.2);
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="text-center mb-4">
            <img src="{{ asset('images/logo.png') }}" alt="Focus" height="56" class="mb-3" onerror="this.style.display='none'">
            <h4 class="fw-bold mb-1" style="color:#0C3D38;">Reset Password</h4>
            <p class="text-muted small mb-0">Enter your email and we'll send you a reset link</p>
        </div>

        @if (session('status'))
            <div class="alert alert-success py-2 small">
                <i class="bi bi-check-circle me-1"></i>{{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger py-2 small">
                <i class="bi bi-exclamation-circle me-1"></i>{{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-4">
                <label for="email" class="form-label fw-semibold small">Email address</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="bi bi-envelope text-muted"></i>
                    </span>
                    <input type="email" id="email" name="email"
                           class="form-control border-start-0 @error('email') is-invalid @enderror"
                           value="{{ old('email') }}"
                           placeholder="you@example.com"
                           autofocus autocomplete="email" required>
                </div>
            </div>

            <button type="submit" class="btn btn-brand w-100 fw-semibold mb-3">
                <i class="bi bi-send me-2"></i>Send Reset Link
            </button>
        </form>

        <div class="text-center">
            <a href="{{ route('login') }}" class="small text-muted text-decoration-none">
                <i class="bi bi-arrow-left me-1"></i>Back to sign in
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
