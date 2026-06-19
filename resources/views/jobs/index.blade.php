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
                    <td class="text-center">
                        <span class="badge bg-{{ $job->status_badge }}">{{ ucfirst(str_replace('_', ' ', $job->status)) }}</span>
                    </td>
                    <td class="text-end">
                        @if($job->status !== 'completed')
                            <button type="button" class="btn btn-sm btn-success"
                                    onclick="completeJobInline({{ $job->id }}, this)"
                                    title="Mark complete">
                                <i class="bi bi-check-lg"></i>
                            </button>
                        @endif
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

    // Selects: submit immediately on change
    form.querySelectorAll('select').forEach(sel => {
        sel.addEventListener('change', submit);
    });

    // Search text: debounce 450ms after last keystroke
    search.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(submit, 450);
    });
})();

async function completeJobInline(jobId, btn) {
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
    try {
        const res = await fetch(`/jobs/${jobId}/complete`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
        });
        if (res.ok) {
            window.location.reload();
        }
    } catch (e) { btn.disabled = false; }
}
</script>
@endpush
