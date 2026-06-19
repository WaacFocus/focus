@extends('layouts.app')

@section('title', 'Jobs')
@section('page-title', 'Jobs')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Jobs</h4>
    <button class="btn btn-primary" type="button" onclick="openJobPanel()">
        <i class="bi bi-plus-lg me-1"></i>New Job
    </button>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by job or client name...">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="pending"     @selected(request('status') === 'pending')>Pending</option>
                    <option value="in_progress" @selected(request('status') === 'in_progress')>In Progress</option>
                    <option value="completed"   @selected(request('status') === 'completed')>Completed</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="assigned_to" class="form-select">
                    <option value="">All Users</option>
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
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Job</th>
                    <th>Client</th>
                    <th>Assigned To</th>
                    <th>Frequency</th>
                    <th>Due Date</th>
                    <th class="text-center">Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($jobs as $job)
                <tr class="{{ $job->status !== 'completed' && $job->due_date->isPast() ? 'table-danger' : '' }}">
                    <td>
                        <div class="fw-semibold">{{ $job->name }}</div>
                        @if($job->description)
                            <small class="text-muted">{{ Str::limit($job->description, 60) }}</small>
                        @endif
                    </td>
                    <td>{{ $job->client?->company_name ?? '—' }}</td>
                    <td>{{ $job->assignedTo->name }}</td>
                    <td><span class="badge bg-light text-dark">{{ $job->frequency_label }}</span></td>
                    <td class="{{ $job->status !== 'completed' && $job->due_date->isPast() ? 'text-danger fw-semibold' : '' }}">
                        {{ $job->due_date->format('d M Y') }}
                        @if($job->status !== 'completed' && $job->due_date->isPast())
                            <span class="badge bg-danger ms-1">Overdue</span>
                        @elseif($job->status !== 'completed' && $job->due_date->isToday())
                            <span class="badge bg-warning ms-1">Today</span>
                        @endif
                    </td>
                    <td class="text-center" style="min-width:140px">
                        <select class="form-select form-select-sm status-select
                                       status-{{ $job->status }}"
                                data-job-id="{{ $job->id }}"
                                data-original="{{ $job->status }}"
                                onchange="updateJobStatus(this)">
                            <option value="pending"     {{ $job->status === 'pending'     ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress" {{ $job->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed"   {{ $job->status === 'completed'   ? 'selected' : '' }}>Completed</option>
                        </select>
                    </td>
                    <td class="text-end">
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

@include('jobs._panel', ['clients' => $clients, 'users' => $users])
@endsection

@push('styles')
<style>
.status-select { border: 0; font-size: .75rem; font-weight: 600; border-radius: .375rem; padding: .2rem .5rem; cursor: pointer; }
.status-select.status-pending     { background: #e2e3e5; color: #41464b; }
.status-select.status-in_progress { background: #cfe2ff; color: #084298; }
.status-select.status-completed   { background: #d1e7dd; color: #0a3622; }
.status-select:focus { box-shadow: none; outline: 2px solid #17B4A7; }
.status-select option { background: #fff; color: #212529; font-weight: 400; }
</style>
@endpush

@push('scripts')
<script>
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
            sel.dataset.original = newStatus;
            sel.className = `form-select form-select-sm status-select status-${newStatus}`;

            const row = sel.closest('tr');
            if (newStatus === 'completed') {
                row.classList.remove('table-danger');
                const data = await res.json();
                if (data.next_due) {
                    showToast(`Completed — next job due ${data.next_due}`);
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
