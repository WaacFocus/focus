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
                <a href="{{ route('jobs.index', ['assigned_to' => auth()->id()]) }}" class="btn btn-sm btn-outline-primary">View all</a>
            </div>
            @if($my_jobs->count())
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle small">
                    <thead class="table-light">
                        <tr>
                            <th>Job</th>
                            <th>Client</th>
                            <th>Frequency</th>
                            <th>Due Date</th>
                            <th class="text-center">Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody id="myJobsTable">
                        @foreach($my_jobs as $job)
                        <tr id="job-row-{{ $job->id }}" class="{{ $job->status !== 'completed' && $job->due_date->isPast() ? 'table-danger' : '' }}">
                            <td>
                                <div class="fw-semibold">{{ $job->name }}</div>
                                @if($job->description)
                                    <span class="text-muted">{{ Str::limit($job->description, 50) }}</span>
                                @endif
                            </td>
                            <td>{{ $job->client?->company_name ?? '—' }}</td>
                            <td><span class="badge bg-light text-dark">{{ $job->frequency_label }}</span></td>
                            <td class="{{ $job->status !== 'completed' && $job->due_date->isPast() ? 'text-danger fw-semibold' : '' }}">
                                {{ $job->due_date->format('d M Y') }}
                                @if($job->due_date->isPast())
                                    <span class="badge bg-danger ms-1">Overdue</span>
                                @elseif($job->due_date->isToday())
                                    <span class="badge bg-warning ms-1">Today</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $job->status_badge }}">{{ ucfirst(str_replace('_', ' ', $job->status)) }}</span>
                            </td>
                            <td class="text-end">
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
                <i class="bi bi-check-circle me-1 text-success"></i>No pending jobs assigned to you. Jobs assigned to you when adding services or from the <a href="{{ route('jobs.index') }}">Jobs</a> page will appear here.
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
async function completeDashboardJob(jobId, btn) {
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
            const data = await res.json();
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
        }
    } catch (e) { btn.disabled = false; }
}
</script>
@endpush
