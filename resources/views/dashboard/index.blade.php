@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
<style>
    .dash-urgent { background: #fff4ee !important; border-left: 3px solid #e85d04 !important; }
    .dash-urgent:hover { background: #ffe8d8 !important; }
</style>
@endpush

@section('content')
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                    <i class="bi bi-people fs-4 text-primary"></i>
                </div>
                <div>
                    <div class="fs-2 fw-bold">{{ $stats['clients'] }}</div>
                    <div class="text-muted small">Active Clients</div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <a href="{{ route('clients.index') }}" class="small text-decoration-none">View all <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="bg-success bg-opacity-10 rounded-3 p-3">
                    <i class="bi bi-kanban fs-4 text-success"></i>
                </div>
                <div>
                    <div class="fs-2 fw-bold">{{ $stats['active_projects'] }}</div>
                    <div class="text-muted small">Active Projects</div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <a href="{{ route('projects.index') }}" class="small text-decoration-none">View all <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                    <i class="bi bi-check2-square fs-4 text-warning"></i>
                </div>
                <div>
                    <div class="fs-2 fw-bold">{{ $stats['pending_tasks'] }}</div>
                    <div class="text-muted small">Open Tasks</div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <a href="{{ route('tasks.index') }}" class="small text-decoration-none">View all <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="bg-danger bg-opacity-10 rounded-3 p-3">
                    <i class="bi bi-arrow-repeat fs-4 text-danger"></i>
                </div>
                <div>
                    <div class="fs-2 fw-bold">{{ $stats['upcoming_renewals'] }}</div>
                    <div class="text-muted small">Renewals Due (30d)</div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <a href="{{ route('renewals.index') }}" class="small text-decoration-none">View all <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold"><i class="bi bi-arrow-repeat me-2 text-danger"></i>Upcoming Renewals</span>
                <a href="{{ route('renewals.create') }}" class="btn btn-sm btn-outline-primary">Add</a>
            </div>
            <div class="list-group list-group-flush">
                @forelse($upcoming_renewals as $renewal)
                    <a href="{{ route('renewals.edit', $renewal) }}" class="list-group-item list-group-item-action">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-medium">{{ $renewal->client->company_name }}</div>
                                <small class="text-muted">{{ $renewal->description }}</small>
                            </div>
                            <div class="text-end">
                                <div class="small fw-semibold {{ $renewal->renewal_date->isPast() ? 'text-danger' : 'text-muted' }}">
                                    {{ $renewal->renewal_date->format('d M Y') }}
                                </div>
                                @if($renewal->amount)
                                    <small class="text-success">£{{ number_format($renewal->amount, 2) }}</small>
                                @endif
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="list-group-item text-muted small">No upcoming renewals in the next 60 days.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold"><i class="bi bi-check2-square me-2 text-warning"></i>Open Tasks</span>
                <a href="{{ route('tasks.create') }}" class="btn btn-sm btn-outline-primary">Add</a>
            </div>
            <div class="list-group list-group-flush">
                @forelse($recent_tasks as $task)
                    <a href="{{ route('tasks.edit', $task) }}"
                       class="list-group-item list-group-item-action {{ $task->is_urgent ? 'dash-urgent' : '' }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="me-2 overflow-hidden">
                                <div class="fw-medium d-flex align-items-center gap-1">
                                    @if($task->is_urgent)
                                        <i class="bi bi-fire text-danger flex-shrink-0" title="Urgent"></i>
                                    @endif
                                    <span>{{ $task->name }}</span>
                                </div>
                                <small class="text-muted">{{ $task->project->client->company_name }} — {{ $task->project->name }}</small>
                            </div>
                            <div class="text-end flex-shrink-0">
                                <span class="badge bg-{{ $task->priority_badge }}">{{ $task->priority }}</span>
                                @if($task->due_date)
                                    <div class="small {{ $task->due_date->isPast() ? 'text-danger' : 'text-muted' }}">
                                        {{ $task->due_date->format('d M') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="list-group-item text-muted small">No open tasks.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold"><i class="bi bi-kanban me-2 text-success"></i>Active Projects</span>
                <a href="{{ route('projects.create') }}" class="btn btn-sm btn-outline-primary">Add</a>
            </div>
            <div class="list-group list-group-flush">
                @forelse($recent_projects as $project)
                    <a href="{{ route('projects.show', $project) }}" class="list-group-item list-group-item-action">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-medium">{{ $project->name }}</div>
                                <small class="text-muted">{{ $project->client->company_name }}</small>
                            </div>
                            @if($project->budget)
                                <small class="text-muted">£{{ number_format($project->budget, 0) }}</small>
                            @endif
                        </div>
                    </a>
                @empty
                    <div class="list-group-item text-muted small">No active projects.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-0">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold"><i class="bi bi-briefcase me-2 text-primary"></i>My Jobs</span>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-secondary" type="button"
                            data-bs-toggle="collapse" data-bs-target="#jobFilters" aria-expanded="{{ request()->hasAny(['jf_status','jf_frequency','jf_client','jf_from','jf_to']) ? 'true' : 'false' }}">
                        <i class="bi bi-funnel me-1"></i>Filter
                        @if(request()->hasAny(['jf_status','jf_frequency','jf_client','jf_from','jf_to']))
                            <span class="badge bg-primary ms-1">On</span>
                        @endif
                    </button>
                    <a href="{{ route('jobs.index', ['assigned_to' => auth()->id()]) }}" class="btn btn-sm btn-outline-primary">View all</a>
                </div>
            </div>

            <div class="collapse {{ request()->hasAny(['jf_status','jf_frequency','jf_client','jf_from','jf_to']) ? 'show' : '' }}" id="jobFilters">
                <form method="GET" action="{{ route('dashboard') }}" class="border-bottom px-3 py-2">
                    <div class="row g-2 align-items-end">
                        <div class="col-sm-6 col-md-2">
                            <label class="form-label form-label-sm mb-1">Status</label>
                            <select name="jf_status" class="form-select form-select-sm">
                                <option value="">All</option>
                                <option value="pending"     {{ request('jf_status') === 'pending'     ? 'selected' : '' }}>Pending</option>
                                <option value="in_progress" {{ request('jf_status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-2">
                            <label class="form-label form-label-sm mb-1">Frequency</label>
                            <select name="jf_frequency" class="form-select form-select-sm">
                                <option value="">All</option>
                                <option value="weekly"    {{ request('jf_frequency') === 'weekly'    ? 'selected' : '' }}>Weekly</option>
                                <option value="monthly"   {{ request('jf_frequency') === 'monthly'   ? 'selected' : '' }}>Monthly</option>
                                <option value="quarterly" {{ request('jf_frequency') === 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                <option value="yearly"    {{ request('jf_frequency') === 'yearly'    ? 'selected' : '' }}>Yearly</option>
                                <option value="one-off"   {{ request('jf_frequency') === 'one-off'   ? 'selected' : '' }}>One-off</option>
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <label class="form-label form-label-sm mb-1">Client</label>
                            <select name="jf_client" class="form-select form-select-sm">
                                <option value="">All Clients</option>
                                @foreach($my_job_clients as $c)
                                    <option value="{{ $c->id }}" {{ request('jf_client') == $c->id ? 'selected' : '' }}>
                                        {{ $c->company_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-2">
                            <label class="form-label form-label-sm mb-1">Due from</label>
                            <input type="date" name="jf_from" class="form-control form-control-sm"
                                   value="{{ request('jf_from') }}">
                        </div>
                        <div class="col-sm-6 col-md-2">
                            <label class="form-label form-label-sm mb-1">Due to</label>
                            <input type="date" name="jf_to" class="form-control form-control-sm"
                                   value="{{ request('jf_to') }}">
                        </div>
                        <div class="col-sm-6 col-md-1 d-flex gap-1">
                            <button type="submit" class="btn btn-sm btn-primary flex-grow-1">
                                <i class="bi bi-search"></i>
                            </button>
                            @if(request()->hasAny(['jf_status','jf_frequency','jf_client','jf_from','jf_to']))
                            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary" title="Clear">
                                <i class="bi bi-x-lg"></i>
                            </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            @if($my_jobs->count())
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle small" id="dashJobsTable">
                    <thead class="table-light">
                        <tr id="dashJobsTableHead">
                            <th data-col="0" style="cursor:grab;user-select:none" title="Drag to reorder">Job</th>
                            <th data-col="1" style="cursor:grab;user-select:none" title="Drag to reorder">Client</th>
                            <th data-col="2" style="cursor:grab;user-select:none" title="Drag to reorder">Frequency</th>
                            <th data-col="3" style="cursor:grab;user-select:none" title="Drag to reorder">Due Date</th>
                            <th data-col="4" class="text-center" style="cursor:grab;user-select:none" title="Drag to reorder">Status</th>
                            <th data-col="fixed" class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody id="myJobsTable">
                        @foreach($my_jobs as $job)
                        <tr id="job-row-{{ $job->id }}" class="{{ $job->status !== 'completed' && $job->due_date->isPast() ? 'table-danger' : '' }}">
                            <td data-col="0">
                                <div class="fw-semibold">{{ $job->name }}</div>
                                @if($job->description)
                                    <span class="text-muted">{{ Str::limit($job->description, 50) }}</span>
                                @endif
                            </td>
                            <td data-col="1">{{ $job->client?->company_name ?? '—' }}</td>
                            <td data-col="2"><span class="badge bg-light text-dark">{{ $job->frequency_label }}</span></td>
                            <td data-col="3" class="{{ $job->status !== 'completed' && $job->due_date->isPast() ? 'text-danger fw-semibold' : '' }}">
                                {{ $job->due_date->format('d M Y') }}
                                @if($job->due_date->isPast())
                                    <span class="badge bg-danger ms-1">Overdue</span>
                                @elseif($job->due_date->isToday())
                                    <span class="badge bg-warning ms-1">Today</span>
                                @endif
                            </td>
                            <td data-col="4" class="text-center">
                                <span class="badge bg-{{ $job->status_badge }}">{{ ucfirst(str_replace('_', ' ', $job->status)) }}</span>
                            </td>
                            <td data-col="fixed" class="text-end">
                                <button class="btn btn-sm btn-success"
                                        onclick="completeDashboardJob({{ $job->id }}, this)"
                                        title="Mark complete">
                                    <i class="bi bi-check-lg"></i> Complete
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="card-body text-muted small">
                @if(request()->hasAny(['jf_status','jf_frequency','jf_client','jf_from','jf_to']))
                    <i class="bi bi-search me-1"></i>No jobs match your filters. <a href="{{ route('dashboard') }}">Clear filters</a>
                @else
                    <i class="bi bi-check-circle me-1 text-success"></i>No pending jobs assigned to you. Jobs assigned to you when adding services or from the <a href="{{ route('jobs.index') }}">Jobs</a> page will appear here.
                @endif
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
(function () {
    const KEY   = 'dash_jobs_col_order';
    const thead = document.getElementById('dashJobsTableHead');
    const table = document.getElementById('dashJobsTable');
    if (!thead || !table) return;

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

    const saved = JSON.parse(localStorage.getItem(KEY) || 'null');
    if (saved) applyOrder(saved);

    Sortable.create(thead, {
        animation: 150,
        filter: '[data-col="fixed"]',
        onEnd() {
            const order = [...thead.children]
                .map(th => th.dataset.col)
                .filter(col => col !== 'fixed');
            applyOrder(order);
            localStorage.setItem(KEY, JSON.stringify(order));
        },
    });
})();

async function completeDashboardJob(jobId, btn) {
    const original = btn.innerHTML;
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
            const row = document.getElementById(`job-row-${jobId}`);
            row.style.transition = 'opacity .3s';
            row.style.opacity = '0';
            setTimeout(() => {
                row.remove();
                const tbody = document.getElementById('myJobsTable');
                if (!tbody || tbody.querySelectorAll('tr').length === 0) {
                    window.location.reload();
                }
            }, 320);
        } else {
            btn.disabled = false;
            btn.innerHTML = original;
            const data = await res.json().catch(() => ({}));
            alert(data.message || 'Could not mark job as complete. Please try again.');
        }
    } catch (e) {
        btn.disabled = false;
        btn.innerHTML = original;
    }
}
</script>
@endpush
