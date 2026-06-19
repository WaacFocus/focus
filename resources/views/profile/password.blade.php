@extends('layouts.app')

@section('title', 'Change Password')
@section('page-title', 'My Details')

@section('content')
<div class="mb-4">
    <h4 class="mb-1">Change Password</h4>
    <p class="text-muted small mb-0">Update the password for your account.</p>
</div>

<div class="row justify-content-start">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">

                <form method="POST" action="{{ route('profile.password.update') }}" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Current Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" name="current_password" id="currentPwd"
                                   class="form-control @error('current_password') is-invalid @enderror"
                                   autocomplete="current-password" autofocus>
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePwd('currentPwd', this)">
                                <i class="bi bi-eye"></i>
                            </button>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="my-3">

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">New Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" name="password" id="newPwd"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Min. 8 characters" autocomplete="new-password">
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePwd('newPwd', this)">
                                <i class="bi bi-eye"></i>
                            </button>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold small">Confirm New Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" name="password_confirmation" id="confirmPwd"
                                   class="form-control"
                                   placeholder="Repeat new password" autocomplete="new-password">
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePwd('confirmPwd', this)">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-check-lg me-1"></i>Update Password
                    </button>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePwd(fieldId, btn) {
    const field  = document.getElementById(fieldId);
    const isText = field.type === 'text';
    field.type   = isText ? 'password' : 'text';
    btn.querySelector('i').className = isText ? 'bi bi-eye' : 'bi bi-eye-slash';
}
</script>
@endpush
