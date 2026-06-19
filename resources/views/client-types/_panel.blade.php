{{-- Shared client type create / edit offcanvas panel --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="typePanel" style="width:420px;">
    <div class="offcanvas-header border-bottom" style="background:var(--brand-dark, #0C3D38);">
        <div>
            <h5 class="offcanvas-title text-white mb-0">
                <i id="typePanelIcon" class="bi bi-building me-2"></i><span id="typePanelTitle">New Client Type</span>
            </h5>
            <small id="typePanelSubtitle" class="text-white-50">Fill in the details below and save</small>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>

    <form id="typeForm" action="{{ route('client-types.store') }}" method="POST" novalidate
          style="display:flex;flex-direction:column;flex:1 1 auto;min-height:0;">
        @csrf
        <input type="hidden" name="_method" value="">

        <div class="offcanvas-body">

            <div id="typeFormSuccess" class="alert alert-success d-none py-2 small mb-3">
                <i class="bi bi-check-circle me-1"></i><span id="typeSuccessMsg"></span>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" placeholder="e.g. Limited Liability Partnership" required>
                <div class="invalid-feedback" data-field="name"></div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Sort Order</label>
                <input type="number" name="sort_order" class="form-control" min="0" value="0" placeholder="0">
                <div class="form-text">Lower numbers appear first in lists.</div>
                <div class="invalid-feedback" data-field="sort_order"></div>
            </div>

            <div class="mb-3" id="isActiveWrapper" style="display:none;">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" id="typeIsActive" value="1" checked>
                    <label class="form-check-label" for="typeIsActive">Active (visible in client forms)</label>
                </div>
            </div>

        </div>

        <div class="offcanvas-footer border-top d-flex gap-2 p-3 bg-white">
            <button type="submit" id="saveTypeBtn" class="btn btn-primary flex-grow-1">
                <span class="btn-label"><i class="bi bi-check-lg me-1"></i><span id="saveTypeBtnText">Create Type</span></span>
                <span class="btn-spinner d-none"><span class="spinner-border spinner-border-sm me-1"></span>Saving…</span>
            </button>
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">Cancel</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
(function () {
    const panelEl  = document.getElementById('typePanel');
    const bsPanel  = new bootstrap.Offcanvas(panelEl);
    const form     = document.getElementById('typeForm');
    const saveBtn  = document.getElementById('saveTypeBtn');
    const csrf     = document.querySelector('meta[name="csrf-token"]').content;
    const storeUrl = '{{ route("client-types.store") }}';

    function setLoading(on) {
        saveBtn.disabled = on;
        saveBtn.querySelector('.btn-label').classList.toggle('d-none', on);
        saveBtn.querySelector('.btn-spinner').classList.toggle('d-none', !on);
    }

    function clearErrors() {
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('[data-field]').forEach(el => el.textContent = '');
        document.getElementById('typeFormSuccess').classList.add('d-none');
    }

    function showErrors(errors) {
        Object.entries(errors).forEach(([field, msgs]) => {
            const input    = form.querySelector(`[name="${field}"]`);
            const feedback = form.querySelector(`[data-field="${field}"]`);
            if (input)    input.classList.add('is-invalid');
            if (feedback) feedback.textContent = msgs[0];
        });
    }

    window.openTypePanel = async function (typeId) {
        form.reset();
        clearErrors();
        document.getElementById('isActiveWrapper').style.display = 'none';

        if (!typeId) {
            panelEl.querySelector('#typePanelIcon').className = 'bi bi-building me-2';
            panelEl.querySelector('#typePanelTitle').textContent = 'New Client Type';
            panelEl.querySelector('#typePanelSubtitle').textContent = 'Fill in the details below and save';
            form.querySelector('[name="sort_order"]').value = '0';
            form.action = storeUrl;
            form.querySelector('[name="_method"]').value = '';
            document.getElementById('saveTypeBtnText').textContent = 'Create Type';
            bsPanel.show();
            return;
        }

        panelEl.querySelector('#typePanelIcon').className = 'bi bi-arrow-clockwise me-2';
        panelEl.querySelector('#typePanelTitle').textContent = 'Loading…';
        panelEl.querySelector('#typePanelSubtitle').textContent = '';
        bsPanel.show();

        try {
            const res  = await fetch(`/client-types/${typeId}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
            });
            const data = await res.json();

            form.querySelector('[name="name"]').value       = data.name ?? '';
            form.querySelector('[name="sort_order"]').value = data.sort_order ?? 0;
            form.querySelector('[name="is_active"]').checked = !!data.is_active;

            document.getElementById('isActiveWrapper').style.display = '';

            panelEl.querySelector('#typePanelIcon').className = 'bi bi-pencil me-2';
            panelEl.querySelector('#typePanelTitle').textContent = data.name;
            panelEl.querySelector('#typePanelSubtitle').textContent = 'Edit client type';
            form.action = `/client-types/${typeId}`;
            form.querySelector('[name="_method"]').value = 'PATCH';
            document.getElementById('saveTypeBtnText').textContent = 'Save Changes';
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
                document.getElementById('typeSuccessMsg').textContent = data.message;
                document.getElementById('typeFormSuccess').classList.remove('d-none');
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
