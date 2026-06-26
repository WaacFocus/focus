{{-- Add service to client — offcanvas panel --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="servicePanel" style="width:500px;">
    <div class="offcanvas-header border-bottom" style="background:var(--brand-dark, #0C3D38);">
        <div>
            <h5 class="offcanvas-title text-white mb-0">
                <i class="bi bi-grid me-2"></i>Add Service
            </h5>
            <small class="text-white-50">Assign a service and create the recurring job</small>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>

    <form id="serviceForm" action="{{ route('clients.services.store', $client) }}" method="POST" novalidate
          style="display:flex;flex-direction:column;flex:1 1 auto;min-height:0;">
        @csrf

        <div class="offcanvas-body">

            <div id="serviceFormSuccess" class="alert alert-success d-none py-2 small mb-3">
                <i class="bi bi-check-circle me-1"></i><span id="serviceSuccessMsg"></span>
            </div>

            {{-- ── Service ────────────────────────────────── --}}
            <p class="text-muted small fw-semibold text-uppercase mb-2" style="letter-spacing:.05em;">Service</p>
            <div class="row g-3 mb-3">
                <div class="col-12">
                    <label class="form-label small fw-semibold">Service <span class="text-danger">*</span></label>
                    <select name="service_id" id="svcSelect" class="form-select form-select-sm" required>
                        <option value="">— Select a service —</option>
                        @foreach($availableServices as $svc)
                            <option value="{{ $svc->id }}">{{ $svc->name }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback" data-field="service_id"></div>
                </div>
                <div class="col-6">
                    <label class="form-label small fw-semibold">Start Date</label>
                    <input type="date" name="start_date" class="form-control form-control-sm">
                    <div class="invalid-feedback" data-field="start_date"></div>
                </div>
                <div class="col-6">
                    <label class="form-label small fw-semibold">End Date</label>
                    <input type="date" name="end_date" class="form-control form-control-sm">
                    <div class="invalid-feedback" data-field="end_date"></div>
                </div>
                <div class="col-12">
                    <label class="form-label small fw-semibold">Service Notes</label>
                    <textarea name="notes" rows="2" class="form-control form-control-sm"
                              placeholder="e.g. special terms, scope notes…"></textarea>
                </div>
            </div>

            <hr class="my-3">

            {{-- ── Job Setup ───────────────────────────────── --}}
            <p class="text-muted small fw-semibold text-uppercase mb-2" style="letter-spacing:.05em;">
                <i class="bi bi-briefcase me-1"></i>Job Setup
            </p>
            <p class="text-muted small mb-3">A recurring job will be created and assigned to the team member below.</p>
            <div class="row g-3">
                <div class="col-6">
                    <label class="form-label small fw-semibold">Frequency <span class="text-danger">*</span></label>
                    <select name="job_frequency" class="form-select form-select-sm" required>
                        <option value="weekly">Weekly</option>
                        <option value="monthly" selected>Monthly</option>
                        <option value="yearly">Yearly</option>
                        <option value="one-off">One-off</option>
                    </select>
                    <div class="invalid-feedback" data-field="job_frequency"></div>
                </div>
                <div class="col-6">
                    <label class="form-label small fw-semibold">First Due Date <span class="text-danger">*</span></label>
                    <input type="date" name="job_due_date" class="form-control form-control-sm" required>
                    <div class="invalid-feedback" data-field="job_due_date"></div>
                </div>
                <div class="col-12">
                    <label class="form-label small fw-semibold">Assign To <span class="text-danger">*</span></label>
                    <select name="job_assigned_to" class="form-select form-select-sm" required>
                        <option value="">— Select user —</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ $user->id === auth()->id() ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback" data-field="job_assigned_to"></div>
                </div>
            </div>

        </div>

        <div class="offcanvas-footer border-top d-flex gap-2 p-3 bg-white">
            <button type="submit" id="saveServiceBtn" class="btn btn-primary flex-grow-1">
                <span class="btn-label"><i class="bi bi-check-lg me-1"></i>Add Service &amp; Create Job</span>
                <span class="btn-spinner d-none"><span class="spinner-border spinner-border-sm me-1"></span>Saving…</span>
            </button>
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">Cancel</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
(function () {
    const form    = document.getElementById('serviceForm');
    const saveBtn = document.getElementById('saveServiceBtn');
    const csrf    = document.querySelector('meta[name="csrf-token"]').content;

    function setLoading(on) {
        saveBtn.disabled = on;
        saveBtn.querySelector('.btn-label').classList.toggle('d-none', on);
        saveBtn.querySelector('.btn-spinner').classList.toggle('d-none', !on);
    }

    function clearErrors() {
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('[data-field]').forEach(el => el.textContent = '');
        document.getElementById('serviceFormSuccess').classList.add('d-none');
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
                document.getElementById('serviceSuccessMsg').textContent = data.message;
                document.getElementById('serviceFormSuccess').classList.remove('d-none');
                setTimeout(() => window.location.reload(), 800);
            }
        } catch (err) {
            console.error(err);
        } finally {
            setLoading(false);
        }
    });

    const panelEl = document.getElementById('servicePanel');
    if (panelEl) {
        panelEl.addEventListener('hidden.bs.offcanvas', clearErrors);
    }
})();
</script>
@endpush
