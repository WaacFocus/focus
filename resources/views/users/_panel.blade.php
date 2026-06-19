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
          style="display:flex;flex-direction:column;flex:1 1 auto;min-height:0;">
        @csrf
        <input type="hidden" name="_method" value="">

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

        </div>

        <div class="offcanvas-footer border-top d-flex gap-2 p-3 bg-white">
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

    window.openUserPanel = async function (userId) {
        form.reset();
        clearErrors();

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
            const res  = await fetch(`/users/${userId}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
            });
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
})();

function togglePwd(fieldId, btn) {
    const field = document.getElementById(fieldId);
    const isText = field.type === 'text';
    field.type = isText ? 'password' : 'text';
    btn.querySelector('i').className = isText ? 'bi bi-eye' : 'bi bi-eye-slash';
}
</script>
@endpush
