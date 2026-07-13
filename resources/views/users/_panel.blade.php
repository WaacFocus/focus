{{-- Shared user create / edit offcanvas panel --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="userPanel" style="width:480px;">
    <div class="offcanvas-header border-bottom" style="background:var(--brand-dark, #0C3D38);">
        <div>
            <h5 class="offcanvas-title text-white mb-0">
                <i id="userPanelIcon" class="bi bi-person-plus me-2"></i><span id="userPanelTitle">New User</span>
            </h5>
            <small id="userPanelSubtitle" class="text-white-50">Fill in the details below and save</small>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>

    <form id="userForm" action="{{ route('users.store') }}" method="POST" novalidate
          style="display:flex;flex-direction:column;flex:1 1 auto;min-height:0;overflow-y:auto;">
        @csrf
        <input type="hidden" name="_method" value="">
        <input type="hidden" id="editingUserId" value="">

        <div class="offcanvas-body">

            <div id="userFormSuccess" class="alert alert-success d-none py-2 small mb-3">
                <i class="bi bi-check-circle me-1"></i><span id="userSuccessMsg"></span>
            </div>

            <p class="text-muted small fw-semibold text-uppercase mb-2" style="letter-spacing:.05em;">Account Details</p>
            <div class="row g-3 mb-3">
                <div class="col-12">
                    <label class="form-label small fw-semibold">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control form-control-sm" required placeholder="e.g. Jane Smith">
                    <div class="invalid-feedback" data-field="name"></div>
                </div>
                <div class="col-12">
                    <label class="form-label small fw-semibold">Email Address <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control form-control-sm" required placeholder="jane@example.com">
                    <div class="invalid-feedback" data-field="email"></div>
                </div>
                <div class="col-12">
                    <label class="form-label small fw-semibold">Role <span class="text-danger">*</span></label>
                    <select name="role" class="form-select form-select-sm" required>
                        <option value="user">User — standard access, no delete or reports</option>
                        <option value="manager">Manager — full access including delete and reports</option>
                    </select>
                    <div class="invalid-feedback" data-field="role"></div>
                </div>
            </div>

            <hr class="my-3">

            <p class="text-muted small fw-semibold text-uppercase mb-2" style="letter-spacing:.05em;">
                Password
                <span id="passwordHint" class="text-muted fw-normal normal-case ms-1" style="text-transform:none;letter-spacing:0;">(leave blank to keep current)</span>
            </p>
            <div class="row g-3 mb-3">
                <div class="col-12">
                    <label class="form-label small fw-semibold">
                        New Password <span id="passwordRequired" class="text-danger">*</span>
                    </label>
                    <div class="input-group input-group-sm">
                        <input type="password" name="password" id="passwordField" class="form-control form-control-sm"
                               placeholder="Min. 8 characters" autocomplete="new-password">
                        <button type="button" class="btn btn-outline-secondary" onclick="togglePwd('passwordField', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <div class="invalid-feedback d-block" data-field="password"></div>
                </div>
                <div class="col-12">
                    <label class="form-label small fw-semibold">Confirm Password <span id="confirmRequired" class="text-danger">*</span></label>
                    <div class="input-group input-group-sm">
                        <input type="password" name="password_confirmation" id="confirmField" class="form-control form-control-sm"
                               placeholder="Repeat password" autocomplete="new-password">
                        <button type="button" class="btn btn-outline-secondary" onclick="togglePwd('confirmField', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- 2FA section — edit mode only --}}
            <div id="twoFactorSection" class="d-none">
                <hr class="my-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <p class="text-muted small fw-semibold text-uppercase mb-0" style="letter-spacing:.05em;">
                        Two-Factor Authentication
                    </p>
                    <button type="button" id="resetAllTwoFactorBtn"
                            class="btn btn-sm btn-outline-danger d-none"
                            onclick="resetAllTwoFactor()">
                        <i class="bi bi-shield-x me-1"></i>Reset All
                    </button>
                </div>

                <div id="twoFactorLoading" class="text-muted small py-2">
                    <span class="spinner-border spinner-border-sm me-1"></span>Loading…
                </div>

                <div id="twoFactorContent" class="d-none">

                    {{-- TOTP row --}}
                    <div class="rounded-3 p-3 mb-2" style="background:#f8f9fa;border:1px solid #e9ecef;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-phone text-muted"></i>
                                <div>
                                    <div class="small fw-semibold">Authenticator App</div>
                                    <div id="totpStatusText" class="small text-muted"></div>
                                </div>
                            </div>
                            <div id="totpActions"></div>
                        </div>

                        {{-- TOTP setup inline --}}
                        <div id="totpSetupArea" class="d-none mt-3">
                            <div class="text-center mb-2">
                                <p class="small text-muted mb-2">Scan with an authenticator app, then enter the 6-digit code to confirm.</p>
                                <div id="totpQrContainer" class="d-inline-block p-2 bg-white border rounded-2 mb-2"></div>
                                <div class="input-group input-group-sm mb-1" style="max-width:200px;margin:0 auto;">
                                    <input type="text" id="adminTotpSecret" class="form-control form-control-sm font-monospace text-center" readonly>
                                    <button class="btn btn-outline-secondary btn-sm" type="button"
                                            onclick="navigator.clipboard.writeText(document.getElementById('adminTotpSecret').value);this.textContent='Copied!'">Copy</button>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <input type="text" id="adminTotpCode" class="form-control form-control-sm font-monospace text-center"
                                       placeholder="000 000" maxlength="6" inputmode="numeric">
                                <button type="button" class="btn btn-sm btn-success flex-shrink-0" onclick="confirmAdminTotp()">
                                    <i class="bi bi-check2 me-1"></i>Confirm
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary flex-shrink-0" onclick="cancelTotpSetup()">
                                    Cancel
                                </button>
                            </div>
                            <div id="adminTotpError" class="text-danger small mt-1 d-none"></div>
                        </div>
                    </div>

                    {{-- Passkeys --}}
                    <div class="rounded-3 p-3" style="background:#f8f9fa;border:1px solid #e9ecef;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-fingerprint text-muted"></i>
                                <div class="small fw-semibold">Passkeys</div>
                                <span id="passkeyCount" class="badge rounded-pill text-bg-secondary ms-1"></span>
                            </div>
                        </div>
                        <div id="passkeyList"></div>
                        <p class="text-muted small mb-0 mt-2">
                            <i class="bi bi-info-circle me-1"></i>Passkeys must be added by the user from their own <strong>Security</strong> page.
                        </p>
                    </div>

                </div>
            </div>

        </div>

        <div class="offcanvas-footer border-top d-flex gap-2 p-3 bg-white flex-shrink-0">
            <button type="submit" id="saveUserBtn" class="btn btn-primary flex-grow-1">
                <span class="btn-label"><i class="bi bi-check-lg me-1"></i><span id="saveUserBtnText">Create User</span></span>
                <span class="btn-spinner d-none"><span class="spinner-border spinner-border-sm me-1"></span>Saving…</span>
            </button>
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">Cancel</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
(function () {
    const panelEl  = document.getElementById('userPanel');
    const bsPanel  = new bootstrap.Offcanvas(panelEl);
    const form     = document.getElementById('userForm');
    const saveBtn  = document.getElementById('saveUserBtn');
    const csrf     = document.querySelector('meta[name="csrf-token"]').content;
    const storeUrl = '{{ route("users.store") }}';

    function setLoading(on) {
        saveBtn.disabled = on;
        saveBtn.querySelector('.btn-label').classList.toggle('d-none', on);
        saveBtn.querySelector('.btn-spinner').classList.toggle('d-none', !on);
    }

    function clearErrors() {
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('[data-field]').forEach(el => el.textContent = '');
        document.getElementById('userFormSuccess').classList.add('d-none');
    }

    function showErrors(errors) {
        Object.entries(errors).forEach(([field, msgs]) => {
            const input    = form.querySelector(`[name="${field}"]`);
            const feedback = form.querySelector(`[data-field="${field}"]`);
            if (input)    input.classList.add('is-invalid');
            if (feedback) feedback.textContent = msgs[0];
        });
        const first = form.querySelector('.is-invalid');
        if (first) first.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function setEditMode(isEdit) {
        const hint     = document.getElementById('passwordHint');
        const req      = document.getElementById('passwordRequired');
        const confReq  = document.getElementById('confirmRequired');
        const pwdField = document.getElementById('passwordField');

        hint.style.display    = isEdit ? '' : 'none';
        req.style.display     = isEdit ? 'none' : '';
        confReq.style.display = isEdit ? 'none' : '';
        pwdField.required     = !isEdit;
    }

    function render2FA(tf) {
        document.getElementById('twoFactorLoading').classList.add('d-none');
        document.getElementById('twoFactorContent').classList.remove('d-none');
        document.getElementById('totpSetupArea').classList.add('d-none');

        const hasAny = tf.totp_enabled || tf.passkeys.length > 0;
        document.getElementById('resetAllTwoFactorBtn').classList.toggle('d-none', !hasAny);

        // TOTP
        const totpText    = document.getElementById('totpStatusText');
        const totpActions = document.getElementById('totpActions');
        if (tf.totp_enabled) {
            totpText.innerHTML = `<span class="text-success"><i class="bi bi-check-circle me-1"></i>Enabled since ${tf.totp_confirmed_at}</span>`;
            totpActions.innerHTML = `<button type="button" class="btn btn-sm btn-outline-danger" onclick="disableTotp()">
                <i class="bi bi-trash me-1"></i>Disable
            </button>`;
        } else {
            totpText.innerHTML = `<span class="text-muted"><i class="bi bi-dash-circle me-1"></i>Not enabled</span>`;
            totpActions.innerHTML = `<button type="button" class="btn btn-sm btn-primary" onclick="startTotpSetup()">
                <i class="bi bi-plus-lg me-1"></i>Set Up
            </button>`;
        }

        // Passkeys
        document.getElementById('passkeyCount').textContent = tf.passkeys.length;
        const list = document.getElementById('passkeyList');
        if (tf.passkeys.length === 0) {
            list.innerHTML = '<p class="text-muted small mb-0">No passkeys registered.</p>';
        } else {
            list.textContent = '';
            tf.passkeys.forEach(p => {
                const row = document.createElement('div');
                row.className = 'd-flex align-items-center justify-content-between py-1';

                const left = document.createElement('div');
                const icon = document.createElement('i');
                icon.className = 'bi bi-key me-1 text-muted small';
                const name = document.createElement('span');
                name.className = 'small fw-medium';
                name.textContent = p.nickname;
                const date = document.createElement('span');
                date.className = 'text-muted small ms-1';
                date.textContent = '· ' + p.created_at;
                left.appendChild(icon);
                left.appendChild(name);
                left.appendChild(date);

                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'btn btn-sm btn-outline-danger py-0 px-2';
                btn.addEventListener('click', () => deletePasskey(p.id));
                const btnIcon = document.createElement('i');
                btnIcon.className = 'bi bi-trash small';
                btn.appendChild(btnIcon);

                row.appendChild(left);
                row.appendChild(btn);
                list.appendChild(row);
            });
        }
    }

    async function load2FA(userId) {
        document.getElementById('twoFactorSection').classList.remove('d-none');
        document.getElementById('twoFactorLoading').classList.remove('d-none');
        document.getElementById('twoFactorContent').classList.add('d-none');

        try {
            const res  = await fetch(`/users/${userId}`, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf } });
            const data = await res.json();
            render2FA(data.two_factor);
        } catch {
            document.getElementById('twoFactorLoading').textContent = 'Could not load 2FA status.';
        }
    }

    window.openUserPanel = async function (userId) {
        form.reset();
        clearErrors();
        document.getElementById('twoFactorSection').classList.add('d-none');
        document.getElementById('editingUserId').value = userId ?? '';

        if (!userId) {
            panelEl.querySelector('#userPanelIcon').className = 'bi bi-person-plus me-2';
            panelEl.querySelector('#userPanelTitle').textContent = 'New User';
            panelEl.querySelector('#userPanelSubtitle').textContent = 'Fill in the details below and save';
            form.action = storeUrl;
            form.querySelector('[name="_method"]').value = '';
            document.getElementById('saveUserBtnText').textContent = 'Create User';
            setEditMode(false);
            bsPanel.show();
            return;
        }

        panelEl.querySelector('#userPanelIcon').className = 'bi bi-arrow-clockwise me-2';
        panelEl.querySelector('#userPanelTitle').textContent = 'Loading…';
        panelEl.querySelector('#userPanelSubtitle').textContent = '';
        bsPanel.show();

        try {
            const res  = await fetch(`/users/${userId}`, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf } });
            const data = await res.json();

            form.querySelector('[name="name"]').value  = data.name  ?? '';
            form.querySelector('[name="email"]').value = data.email ?? '';
            form.querySelector('[name="role"]').value  = data.role  ?? 'user';

            panelEl.querySelector('#userPanelIcon').className = 'bi bi-pencil me-2';
            panelEl.querySelector('#userPanelTitle').textContent = data.name;
            panelEl.querySelector('#userPanelSubtitle').textContent = 'Edit user details';
            form.action = `/users/${userId}`;
            form.querySelector('[name="_method"]').value = 'PATCH';
            document.getElementById('saveUserBtnText').textContent = 'Save Changes';
            setEditMode(true);

            render2FA(data.two_factor);
            document.getElementById('twoFactorSection').classList.remove('d-none');
            document.getElementById('twoFactorLoading').classList.add('d-none');
            document.getElementById('twoFactorContent').classList.remove('d-none');

        } catch (err) {
            console.error(err);
            bsPanel.hide();
        }
    };

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        clearErrors();
        setLoading(true);

        try {
            const res  = await fetch(form.action, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                body: new FormData(form),
            });
            const data = await res.json();

            if (res.status === 422) {
                showErrors(data.errors);
            } else if (res.ok) {
                document.getElementById('userSuccessMsg').textContent = data.message;
                document.getElementById('userFormSuccess').classList.remove('d-none');
                setTimeout(() => window.location.reload(), 800);
            }
        } catch (err) {
            console.error(err);
        } finally {
            setLoading(false);
        }
    });

    panelEl.addEventListener('hidden.bs.offcanvas', function () {
        form.reset();
        clearErrors();
    });

    // — 2FA admin actions —

    window.disableTotp = async function () {
        const userId = document.getElementById('editingUserId').value;
        if (!confirm('Remove this user\'s authenticator app?')) return;
        await twoFactorAction(`/users/${userId}/2fa/totp/disable`, 'POST');
        await load2FA(userId);
    };

    window.deletePasskey = async function (credId) {
        const userId = document.getElementById('editingUserId').value;
        if (!confirm('Remove this passkey?')) return;
        await twoFactorAction(`/users/${userId}/2fa/passkeys/${credId}`, 'DELETE');
        await load2FA(userId);
    };

    window.resetAllTwoFactor = async function () {
        const userId = document.getElementById('editingUserId').value;
        if (!confirm('Remove ALL two-factor methods for this user? They will no longer require 2FA to log in.')) return;
        await twoFactorAction(`/users/${userId}/2fa/reset`, 'POST');
        await load2FA(userId);
    };

    async function twoFactorAction(url, method) {
        const body = method === 'DELETE' ? null : new FormData();
        const res  = await fetch(url, {
            method,
            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            body,
        });
        if (!res.ok) {
            const d = await res.json().catch(() => ({}));
            alert(d.message || 'An error occurred.');
        }
    }

    // — TOTP setup flow —

    window.startTotpSetup = async function () {
        const userId = document.getElementById('editingUserId').value;
        const area   = document.getElementById('totpSetupArea');
        const qrEl   = document.getElementById('totpQrContainer');
        const errEl  = document.getElementById('adminTotpError');

        // Swap button to loading state
        document.querySelector('#totpActions button').disabled = true;
        document.querySelector('#totpActions button').innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        errEl.classList.add('d-none');

        try {
            const res  = await fetch(`/users/${userId}/2fa/totp/generate`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            });
            const data = await res.json();

            qrEl.innerHTML = data.qr_svg;
            document.getElementById('adminTotpSecret').value = data.secret;
            document.getElementById('adminTotpCode').value   = '';
            area.classList.remove('d-none');

            // Restore button
            document.querySelector('#totpActions button').disabled = false;
            document.querySelector('#totpActions button').innerHTML = '<i class="bi bi-plus-lg me-1"></i>Set Up';
        } catch {
            alert('Could not generate QR code. Please try again.');
            document.querySelector('#totpActions button').disabled = false;
            document.querySelector('#totpActions button').innerHTML = '<i class="bi bi-plus-lg me-1"></i>Set Up';
        }
    };

    window.confirmAdminTotp = async function () {
        const userId = document.getElementById('editingUserId').value;
        const code   = document.getElementById('adminTotpCode').value.trim();
        const errEl  = document.getElementById('adminTotpError');
        errEl.classList.add('d-none');

        if (code.length !== 6) {
            errEl.textContent = 'Please enter the 6-digit code from the authenticator app.';
            errEl.classList.remove('d-none');
            return;
        }

        const body = new FormData();
        body.append('code', code);

        const res  = await fetch(`/users/${userId}/2fa/totp/confirm`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            body,
        });
        const data = await res.json();

        if (res.status === 422) {
            errEl.textContent = data.errors?.code?.[0] ?? data.message ?? 'Invalid code.';
            errEl.classList.remove('d-none');
            return;
        }

        if (res.ok) {
            await load2FA(userId);
        }
    };

    window.cancelTotpSetup = function () {
        document.getElementById('totpSetupArea').classList.add('d-none');
    };
})();

function togglePwd(fieldId, btn) {
    const field = document.getElementById(fieldId);
    const isText = field.type === 'text';
    field.type = isText ? 'password' : 'text';
    btn.querySelector('i').className = isText ? 'bi bi-eye' : 'bi bi-eye-slash';
}
</script>
@endpush
