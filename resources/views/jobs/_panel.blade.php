{{-- Shared job create / edit offcanvas panel --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="jobPanel" style="width: 520px;">
    <div class="offcanvas-header border-bottom" style="background: var(--brand-dark, #0C3D38);">
        <div>
            <h5 class="offcanvas-title text-white mb-0">
                <i id="jobPanelIcon" class="bi bi-briefcase me-2"></i><span id="jobPanelTitle">New Job</span>
            </h5>
            <small id="jobPanelSubtitle" class="text-white-50">Fill in the details below and save</small>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>

    <form id="jobForm" action="{{ route('jobs.store') }}" method="POST" novalidate
          style="display:flex;flex-direction:column;flex:1 1 auto;min-height:0;">
        @csrf
        <input type="hidden" name="_method" value="">

        <div class="offcanvas-body">

            <div id="jobFormSuccess" class="alert alert-success d-none py-2 small mb-3">
                <i class="bi bi-check-circle me-1"></i><span id="jobSuccessMsg"></span>
            </div>

            <p class="text-muted small fw-semibold text-uppercase mb-2" style="letter-spacing:.05em;">Job Details</p>
            <div class="row g-3 mb-3">
                <div class="col-12">
                    <label class="form-label small fw-semibold">Job Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control form-control-sm" required placeholder="e.g. Prepare VAT Return">
                    <div class="invalid-feedback" data-field="name"></div>
                </div>
                <div class="col-12">
                    <label class="form-label small fw-semibold">Description</label>
                    <textarea name="description" rows="2" class="form-control form-control-sm" placeholder="Optional details..."></textarea>
                    <div class="invalid-feedback" data-field="description"></div>
                </div>
                <div class="col-6">
                    <label class="form-label small fw-semibold">Client</label>
                    <select name="client_id" class="form-select form-select-sm">
                        <option value="">— No client —</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->company_name }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback" data-field="client_id"></div>
                </div>
                <div class="col-6">
                    <label class="form-label small fw-semibold">Assigned To <span class="text-danger">*</span></label>
                    <select name="assigned_to" class="form-select form-select-sm" required>
                        <option value="">— Select user —</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ $user->id === auth()->id() ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback" data-field="assigned_to"></div>
                </div>
            </div>

            <hr class="my-3">

            <p class="text-muted small fw-semibold text-uppercase mb-2" style="letter-spacing:.05em;">Schedule</p>
            <div class="row g-3 mb-3">
                <div class="col-4">
                    <label class="form-label small fw-semibold">Frequency <span class="text-danger">*</span></label>
                    <select name="frequency" class="form-select form-select-sm" required>
                        <option value="weekly">Weekly</option>
                        <option value="monthly" selected>Monthly</option>
                        <option value="quarterly">Quarterly</option>
                        <option value="yearly">Yearly</option>
                        <option value="one-off">One-off</option>
                    </select>
                    <div class="invalid-feedback" data-field="frequency"></div>
                </div>
                <div class="col-4">
                    <label class="form-label small fw-semibold">Due Date <span class="text-danger">*</span></label>
                    <input type="date" name="due_date" class="form-control form-control-sm" required>
                    <div class="invalid-feedback" data-field="due_date"></div>
                </div>
                <div class="col-4">
                    <label class="form-label small fw-semibold">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="pending" selected>Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                    </select>
                    <div class="invalid-feedback" data-field="status"></div>
                </div>
            </div>

            <hr class="my-3">

            <p class="text-muted small fw-semibold text-uppercase mb-2" style="letter-spacing:.05em;">Notes</p>
            <textarea name="notes" rows="3" class="form-control form-control-sm mb-3" placeholder="Any additional notes..."></textarea>

        </div>

        <div class="offcanvas-footer border-top d-flex gap-2 p-3 bg-white">
            <button type="submit" id="saveJobBtn" class="btn btn-primary flex-grow-1">
                <span class="btn-label"><i class="bi bi-check-lg me-1"></i><span id="saveJobBtnText">Create Job</span></span>
                <span class="btn-spinner d-none"><span class="spinner-border spinner-border-sm me-1"></span>Saving…</span>
            </button>
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">Cancel</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
(function () {
    const panelEl  = document.getElementById('jobPanel');
    const bsPanel  = new bootstrap.Offcanvas(panelEl);
    const form     = document.getElementById('jobForm');
    const saveBtn  = document.getElementById('saveJobBtn');
    const csrf     = document.querySelector('meta[name="csrf-token"]').content;
    const storeUrl = '{{ route("jobs.store") }}';

    function setLoading(on) {
        saveBtn.disabled = on;
        saveBtn.querySelector('.btn-label').classList.toggle('d-none', on);
        saveBtn.querySelector('.btn-spinner').classList.toggle('d-none', !on);
    }

    function clearErrors() {
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('[data-field]').forEach(el => el.textContent = '');
        document.getElementById('jobFormSuccess').classList.add('d-none');
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

    function setField(name, value) {
        const el = form.querySelector(`[name="${name}"]`);
        if (!el) return;
        el.value = value ?? '';
    }

    window.openJobPanel = async function (jobId) {
        form.reset();
        clearErrors();

        if (!jobId) {
            panelEl.querySelector('#jobPanelIcon').className = 'bi bi-briefcase me-2';
            panelEl.querySelector('#jobPanelTitle').textContent = 'New Job';
            panelEl.querySelector('#jobPanelSubtitle').textContent = 'Fill in the details below and save';
            form.action = storeUrl;
            form.querySelector('[name="_method"]').value = '';
            document.getElementById('saveJobBtnText').textContent = 'Create Job';
            bsPanel.show();
            return;
        }

        panelEl.querySelector('#jobPanelIcon').className = 'bi bi-arrow-clockwise me-2';
        panelEl.querySelector('#jobPanelTitle').textContent = 'Loading…';
        panelEl.querySelector('#jobPanelSubtitle').textContent = '';
        bsPanel.show();

        try {
            const res  = await fetch(`/jobs/${jobId}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
            });
            const data = await res.json();

            ['name','description','client_id','assigned_to','frequency','status','notes']
                .forEach(f => setField(f, data[f]));

            setField('due_date', data.due_date ? data.due_date.substring(0, 10) : '');

            panelEl.querySelector('#jobPanelIcon').className = 'bi bi-pencil me-2';
            panelEl.querySelector('#jobPanelTitle').textContent = data.name;
            panelEl.querySelector('#jobPanelSubtitle').textContent = 'Edit job details';
            form.action = `/jobs/${jobId}`;
            form.querySelector('[name="_method"]').value = 'PATCH';
            document.getElementById('saveJobBtnText').textContent = 'Save Changes';
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
                document.getElementById('jobSuccessMsg').textContent = data.message;
                document.getElementById('jobFormSuccess').classList.remove('d-none');
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
</script>
@endpush
