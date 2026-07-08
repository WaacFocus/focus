@extends('layouts.app')

@section('title', 'Job Statuses')
@section('page-title', 'Admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Job Statuses</h4>
        <p class="text-muted small mb-0">Define statuses per service or globally. Drag rows to reorder.</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#statusModal" onclick="openAddModal()">
        <i class="bi bi-plus-lg me-1"></i>Add Status
    </button>
</div>

@if(session('success'))
    <div class="alert alert-success py-2 small mb-3">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger py-2 small mb-3">{{ $errors->first() }}</div>
@endif

{{-- Global Statuses --}}
@include('admin.job-statuses._group', [
    'groupLabel'  => 'Global',
    'groupSub'    => 'Shown for jobs with no service assigned',
    'statuses'    => $globalStatuses,
    'serviceId'   => null,
    'groupKey'    => 'global',
])

{{-- Per-service groups --}}
@foreach($services as $service)
    @php $svcStatuses = $serviceStatuses->get($service->id, collect()); @endphp
    @include('admin.job-statuses._group', [
        'groupLabel' => $service->name,
        'groupSub'   => 'Overrides global statuses for jobs linked to this service',
        'statuses'   => $svcStatuses,
        'serviceId'  => $service->id,
        'groupKey'   => 'svc-' . $service->id,
    ])
@endforeach

{{-- Add / Edit Modal --}}
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="statusModalForm">
                @csrf
                <input type="hidden" name="_method" id="modalMethod" value="POST">
                <div class="modal-header" style="background:var(--brand-dark);color:#fff;">
                    <h5 class="modal-title" id="statusModalTitle">Add Status</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="modalName" class="form-control" required placeholder="e.g. In Review">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Color <span class="text-danger">*</span></label>
                            <select name="color" id="modalColor" class="form-select" required>
                                <option value="secondary">Grey</option>
                                <option value="in-progress">Blue</option>
                                <option value="success">Green</option>
                                <option value="warning">Yellow</option>
                                <option value="danger">Red</option>
                                <option value="info">Cyan</option>
                                <option value="primary">Teal</option>
                                <option value="dark">Dark</option>
                            </select>
                        </div>
                        <div class="col-12" id="modalSlugRow">
                            <label class="form-label fw-semibold">Slug <span class="text-danger">*</span></label>
                            <input type="text" name="slug" id="modalSlug" class="form-control font-monospace" placeholder="e.g. in_review" pattern="[a-z0-9_]+">
                            <div class="form-text">Lowercase letters, numbers and underscores only. Cannot be changed after creation.</div>
                        </div>
                        <div class="col-12" id="modalSlugReadonly" style="display:none;">
                            <label class="form-label fw-semibold">Slug</label>
                            <input type="text" id="modalSlugDisplay" class="form-control font-monospace bg-light" readonly>
                            <div class="form-text">Slug cannot be changed after creation (it is stored on existing jobs).</div>
                        </div>
                        <div class="col-12" id="modalServiceRow">
                            <label class="form-label fw-semibold">Group</label>
                            <select name="service_id" id="modalServiceId" class="form-select">
                                <option value="">Global (all jobs)</option>
                                @foreach($services as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_completion" id="modalIsCompletion" value="1">
                                <label class="form-check-label fw-semibold" for="modalIsCompletion">Completion status</label>
                            </div>
                            <div class="form-text">When a job is set to this status, it is treated as done and the next recurring job is scheduled.</div>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="modalIsActive" value="1" checked>
                                <label class="form-check-label" for="modalIsActive">Active (visible in dropdowns)</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

// Auto-generate slug from name on create
document.getElementById('modalName').addEventListener('input', function () {
    const slugInput = document.getElementById('modalSlug');
    if (slugInput && slugInput.closest('#modalSlugRow').style.display !== 'none') {
        slugInput.value = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_+|_+$/g, '');
    }
});

function openAddModal(serviceId) {
    const form = document.getElementById('statusModalForm');
    form.action = '{{ route('admin.job-statuses.store') }}';
    document.getElementById('modalMethod').value = 'POST';
    document.getElementById('statusModalTitle').textContent = 'Add Status';

    form.reset();
    document.getElementById('modalSlugRow').style.display = '';
    document.getElementById('modalSlugReadonly').style.display = 'none';
    document.getElementById('modalServiceRow').style.display = '';
    document.getElementById('modalIsActive').checked = true;

    if (serviceId) {
        document.getElementById('modalServiceId').value = serviceId;
    }
}

function openEditModal(id, name, color, slug, serviceId, isCompletion, isActive) {
    const form = document.getElementById('statusModalForm');
    form.action = '/admin/job-statuses/' + id;
    document.getElementById('modalMethod').value = 'PATCH';
    document.getElementById('statusModalTitle').textContent = 'Edit Status';

    document.getElementById('modalName').value         = name;
    document.getElementById('modalColor').value        = color;
    document.getElementById('modalSlugDisplay').value  = slug;
    document.getElementById('modalIsCompletion').checked = isCompletion;
    document.getElementById('modalIsActive').checked   = isActive;

    // Slug is read-only on edit
    document.getElementById('modalSlugRow').style.display     = 'none';
    document.getElementById('modalSlugReadonly').style.display = '';
    // Service group is fixed on edit
    document.getElementById('modalServiceRow').style.display   = 'none';

    new bootstrap.Modal(document.getElementById('statusModal')).show();
}

// Sortable per group
document.querySelectorAll('[data-sortable-group]').forEach(function (tbody) {
    Sortable.create(tbody, {
        handle: '.drag-handle',
        animation: 150,
        onEnd: function () {
            const ids = [...tbody.querySelectorAll('tr[data-id]')].map(r => r.dataset.id);
            const saveBtn = document.getElementById('saveOrderBtn-' + tbody.dataset.sortableGroup);
            if (saveBtn) {
                saveBtn.style.display = '';
                saveBtn.dataset.ids   = JSON.stringify(ids);
            }
        },
    });
});

function saveOrder(groupKey) {
    const saveBtn = document.getElementById('saveOrderBtn-' + groupKey);
    const ids     = JSON.parse(saveBtn.dataset.ids);
    saveBtn.disabled    = true;
    saveBtn.textContent = 'Saving…';

    fetch('{{ route('admin.job-statuses.reorder') }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ ids }),
    }).then(function (res) {
        saveBtn.style.display = 'none';
        if (!res.ok) alert('Failed to save order.');
    });
}
</script>
@endpush
