@extends('layouts.app')

@section('title', 'Jobs')
@section('page-title', 'Jobs')

@section('content')
<div class="mb-4">
    <h4 class="mb-0">Jobs</h4>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by job or client name...">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value=""    @selected(!request()->filled('status'))>Active Jobs</option>
                    <option value="all" @selected(request('status') === 'all')>All (inc. completed)</option>
                    @foreach($statuses as $s)
                        <option value="{{ $s->slug }}" @selected(request('status') === $s->slug)>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="assigned_to" class="form-select">
                    <option value=""    @selected(!request()->filled('assigned_to'))>My Jobs</option>
                    <option value="all" @selected(request('assigned_to') === 'all')>All Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @selected(request('assigned_to') == $user->id)>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="days" class="form-select">
                    <option value=""    @selected(!request('days'))>All time</option>
                    <option value="30"  @selected(request('days') === '30')>Next 30 days</option>
                    <option value="60"  @selected(request('days') === '60')>Next 60 days</option>
                    <option value="90"  @selected(request('days') === '90')>Next 90 days</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2 align-items-center">
                <span id="filterSpinner" class="text-muted small d-none">
                    <span class="spinner-border spinner-border-sm me-1"></span>Updating…
                </span>
                <a href="{{ route('jobs.index') }}" class="btn btn-outline-secondary ms-auto">Clear</a>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                        <i class="bi bi-layout-three-columns me-1"></i>Columns
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end p-2" style="min-width:160px;" id="jobColMenu">
                        <li><label class="dropdown-item rounded d-flex align-items-center gap-2 py-1 px-2"><input type="checkbox" class="job-col-check" data-col="1" checked> Client</label></li>
                        <li><label class="dropdown-item rounded d-flex align-items-center gap-2 py-1 px-2"><input type="checkbox" class="job-col-check" data-col="2" checked> Assigned To</label></li>
                        <li><label class="dropdown-item rounded d-flex align-items-center gap-2 py-1 px-2"><input type="checkbox" class="job-col-check" data-col="3" checked> Frequency</label></li>
                        <li><label class="dropdown-item rounded d-flex align-items-center gap-2 py-1 px-2"><input type="checkbox" class="job-col-check" data-col="4" checked> Due Date</label></li>
                        <li><label class="dropdown-item rounded d-flex align-items-center gap-2 py-1 px-2"><input type="checkbox" class="job-col-check" data-col="5" checked> Status</label></li>
                    </ul>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle" id="jobsTable">
            <thead class="table-light">
                <tr id="jobsTableHead">
                    <th data-col="0" style="cursor:grab;user-select:none" title="Drag to reorder">Job</th>
                    <th data-col="1" style="cursor:grab;user-select:none" title="Drag to reorder">Client</th>
                    <th data-col="2" style="cursor:grab;user-select:none" title="Drag to reorder">Assigned To</th>
                    <th data-col="3" style="cursor:grab;user-select:none" title="Drag to reorder">Frequency</th>
                    <th data-col="4" style="cursor:grab;user-select:none" title="Drag to reorder">Due Date</th>
                    <th data-col="5" class="text-center" style="cursor:grab;user-select:none" title="Drag to reorder">Status</th>
                    <th data-col="fixed"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($jobs as $job)
                <tr class="{{ !$job->isComplete() && $job->due_date->isPast() ? 'table-danger' : '' }}" data-complete="{{ $job->isComplete() ? '1' : '0' }}">
                    <td data-col="0">
                        <div class="fw-semibold">{{ $job->name }}</div>
                        @if($job->description)
                            <small class="text-muted">{{ Str::limit($job->description, 60) }}</small>
                        @endif
                    </td>
                    <td data-col="1">{{ $job->client?->company_name ?? '—' }}</td>
                    <td data-col="2">{{ $job->assignedTo->name }}</td>
                    <td data-col="3"><span class="badge bg-light text-dark">{{ $job->frequency_label }}</span></td>
                    <td data-col="4" class="{{ !$job->isComplete() && $job->due_date->isPast() ? 'text-danger fw-semibold' : '' }}">
                        {{ $job->due_date->format('d M Y') }}
                        @if(!$job->isComplete() && $job->due_date->isPast())
                            <span class="badge bg-danger ms-1">Overdue</span>
                        @elseif(!$job->isComplete() && $job->due_date->isToday())
                            <span class="badge bg-warning ms-1">Today</span>
                        @endif
                    </td>
                    <td data-col="5" class="text-center" style="min-width:140px">
                        <select class="form-select form-select-sm status-select"
                                data-job-id="{{ $job->id }}"
                                data-service-id="{{ $job->service_id }}"
                                data-original="{{ $job->status }}"
                                onchange="updateJobStatus(this)">
                            {{-- options populated by JS --}}
                        </select>
                    </td>
                    <td data-col="fixed" class="text-end">
                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                onclick="openJobPanel({{ $job->id }})" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>
                        @can('manager')
                        <form method="POST" action="{{ route('jobs.destroy', $job) }}" class="d-inline"
                              onsubmit="return confirm('Delete this job?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No jobs found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($jobs->hasPages())
        <div class="card-footer bg-white">{{ $jobs->links() }}</div>
    @endif
</div>

@include('jobs._panel', ['clients' => $clients, 'users' => $users, 'services' => $services, 'statusesByService' => $statusesByService])
@endsection

@push('styles')
<style>
.job-col-check {
    width: 1em; height: 1em; margin-top: 0;
    appearance: none; -webkit-appearance: none;
    border: 1.5px solid #adb5bd; border-radius: .2em;
    background: #fff; cursor: pointer; flex-shrink: 0;
    transition: background .12s, border-color .12s;
}
.job-col-check:checked {
    background-color: var(--brand-dark);
    border-color: var(--brand-dark);
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%23fff' stroke-width='3' d='m6 10 3 3 5-5'/%3e%3c/svg%3e");
    background-repeat: no-repeat; background-size: cover;
}
.job-col-check:focus { outline: none; box-shadow: 0 0 0 .2rem rgba(12,61,56,.25); }
.status-select { border: 0; font-size: .75rem; font-weight: 600; border-radius: .375rem; padding: .2rem .5rem; cursor: pointer; }
.status-select:focus { box-shadow: none; outline: 2px solid #17B4A7; }
.status-select option { background: #fff; color: #212529; font-weight: 400; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
// ── Column visibility toggle ─────────────────────────────────────────────────
(function () {
    const STORAGE_KEY = 'jobs_col_visibility';
    const checkboxes  = document.querySelectorAll('.job-col-check');

    function applyVisibility(col, visible) {
        document.querySelectorAll(`#jobsTable [data-col="${col}"]`).forEach(function (el) {
            el.style.display = visible ? '' : 'none';
        });
    }

    function savePrefs() {
        const prefs = {};
        checkboxes.forEach(function (cb) { prefs[cb.dataset.col] = cb.checked; });
        localStorage.setItem(STORAGE_KEY, JSON.stringify(prefs));
    }

    function loadPrefs() {
        try {
            const saved = JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');
            checkboxes.forEach(function (cb) {
                if (cb.dataset.col in saved) cb.checked = saved[cb.dataset.col];
                applyVisibility(cb.dataset.col, cb.checked);
            });
        } catch (e) {}
    }

    checkboxes.forEach(function (cb) {
        cb.addEventListener('change', function () {
            applyVisibility(cb.dataset.col, cb.checked);
            savePrefs();
        });
    });

    loadPrefs();
})();
</script>
<script>
(function () {
    const PREF_KEY  = 'jobs_col_order';
    const SAVE_URL  = '{{ route('profile.preferences.save') }}';
    const CSRF      = document.querySelector('meta[name="csrf-token"]').content;
    const thead     = document.getElementById('jobsTableHead');
    const table     = document.getElementById('jobsTable');

    function applyOrder(order) {
        const thFixed = thead.querySelector('[data-col="fixed"]');
        const thFrag  = document.createDocumentFragment();
        order.forEach(col => {
            const th = thead.querySelector(`[data-col="${col}"]`);
            if (th) thFrag.appendChild(th);
        });
        thead.insertBefore(thFrag, thFixed);

        table.querySelectorAll('tbody tr').forEach(row => {
            const tdFixed = row.querySelector('[data-col="fixed"]');
            if (!tdFixed) return;
            const tdFrag = document.createDocumentFragment();
            order.forEach(col => {
                const td = row.querySelector(`[data-col="${col}"]`);
                if (td) tdFrag.appendChild(td);
            });
            row.insertBefore(tdFrag, tdFixed);
        });
    }

    // Server-saved order takes priority; fall back to localStorage
    const serverOrder = @json(auth()->user()->preferences['jobs_col_order'] ?? null);
    const localOrder  = JSON.parse(localStorage.getItem(PREF_KEY) || 'null');
    const saved = serverOrder || localOrder;
    if (saved) applyOrder(saved);

    Sortable.create(thead, {
        animation: 150,
        filter: '[data-col="fixed"]',
        onEnd() {
            const order = [...thead.children]
                .map(th => th.dataset.col)
                .filter(col => col !== 'fixed');
            applyOrder(order);
            // Persist locally immediately, then save to server
            localStorage.setItem(PREF_KEY, JSON.stringify(order));
            fetch(SAVE_URL, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ key: PREF_KEY, value: order }),
            });
        },
    });
})();

(function () {
    const form    = document.querySelector('.card.shadow-sm.mb-4 form');
    const search  = form.querySelector('[name="search"]');
    const spinner = document.getElementById('filterSpinner');
    let   timer;

    function submit() {
        spinner.classList.remove('d-none');
        form.submit();
    }

    form.querySelectorAll('select').forEach(sel => sel.addEventListener('change', submit));
    search.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(submit, 450);
    });
})();

// Status colour map
const STATUS_COLORS = {
    'secondary':   { bg: '#e2e3e5', color: '#41464b' },
    'in-progress': { bg: '#cfe2ff', color: '#084298' },
    'success':     { bg: '#d1e7dd', color: '#0a3622' },
    'warning':     { bg: '#fff3cd', color: '#664d03' },
    'danger':      { bg: '#f8d7da', color: '#58151c' },
    'info':        { bg: '#cff4fc', color: '#055160' },
    'primary':     { bg: '#17B4A7', color: '#fff' },
    'dark':        { bg: '#343a40', color: '#fff' },
};

const statusesByService = @json($statusesByService);
const completionSlugs  = @json($completionSlugs);

function getStatusList(serviceId) {
    const sid = serviceId ? String(serviceId) : null;
    if (sid && statusesByService[sid] && statusesByService[sid].length) return statusesByService[sid];
    return statusesByService['global'] || [];
}

function applyStatusColor(sel, colorKey) {
    const c = STATUS_COLORS[colorKey] || STATUS_COLORS['secondary'];
    sel.style.background = c.bg;
    sel.style.color      = c.color;
}

// Initialise all status selects in the table
document.querySelectorAll('.status-select').forEach(function (sel) {
    const list    = getStatusList(sel.dataset.serviceId);
    const current = sel.dataset.original;
    sel.innerHTML = list.map(function (s) {
        return '<option value="' + s.slug + '"' + (s.slug === current ? ' selected' : '') + '>' + s.name + '</option>';
    }).join('');
    const currentStatus = list.find(function (s) { return s.slug === current; });
    if (currentStatus) applyStatusColor(sel, currentStatus.color);
});

async function updateJobStatus(sel) {
    const jobId    = sel.dataset.jobId;
    const newStatus = sel.value;
    const original = sel.dataset.original;

    sel.disabled = true;

    try {
        const res = await fetch(`/jobs/${jobId}/status`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ status: newStatus }),
        });

        if (res.ok) {
            const data = await res.json();
            sel.dataset.original = newStatus;

            const list = getStatusList(sel.dataset.serviceId);
            const statusObj = list.find(function (s) { return s.slug === newStatus; });
            if (statusObj) applyStatusColor(sel, statusObj.color);

            const row = sel.closest('tr');
            if (data.is_completion) {
                row.classList.remove('table-danger');
                row.dataset.complete = '1';
                row.querySelectorAll('.badge.bg-danger.ms-1, .badge.bg-warning.ms-1').forEach(function (b) { b.remove(); });
                if (data.next_due) {
                    showToast('Completed — next job due ' + data.next_due);
                }
            }
        } else {
            sel.value = original;
        }
    } catch (e) {
        sel.value = original;
    } finally {
        sel.disabled = false;
    }
}

function showToast(msg) {
    const el = document.createElement('div');
    el.className = 'position-fixed bottom-0 end-0 p-3';
    el.style.zIndex = 9999;
    el.innerHTML = `<div class="toast align-items-center text-bg-success border-0 show" role="alert">
        <div class="d-flex"><div class="toast-body">${msg}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" onclick="this.closest('.position-fixed').remove()"></button>
        </div></div>`;
    document.body.appendChild(el);
    setTimeout(() => el.remove(), 4000);
}
</script>
@endpush
