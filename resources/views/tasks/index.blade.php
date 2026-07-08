@extends('layouts.app')

@section('title', 'Tasks')
@section('page-title', 'Tasks')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Tasks</h4>
    <a href="{{ route('tasks.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New Task</a>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search tasks...">
            </div>
            <div class="col-md-2">
                <select name="assigned" class="form-select auto-filter">
                    <option value="me"  @selected(request('assigned', 'me') === 'me')>My Tasks</option>
                    <option value="all" @selected(request('assigned') === 'all')>All Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @selected(request('assigned') == $user->id)>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select auto-filter">
                    <option value="">All Statuses</option>
                    @foreach(['pending','in_progress','completed','cancelled'] as $s)
                        <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="priority" class="form-select auto-filter">
                    <option value="">All Priorities</option>
                    <option value="high"   @selected(request('priority') === 'high')>High</option>
                    <option value="medium" @selected(request('priority') === 'medium')>Medium</option>
                    <option value="low"    @selected(request('priority') === 'low')>Low</option>
                </select>
            </div>
            <div class="col-md-1">
                <select name="urgent" class="form-select auto-filter">
                    <option value="">All</option>
                    <option value="1" @selected(request('urgent') === '1')>Urgent</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-outline-secondary flex-grow-1">Filter</button>
                <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width:36px;"></th>
                    <th>Task</th>
                    <th>Assigned to</th>
                    <th class="text-center">Priority</th>
                    <th class="text-center">Status</th>
                    <th>Due Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($tasks as $task)
                <tr class="{{ $task->is_urgent ? 'urgent-row' : ($task->status === 'completed' ? 'text-muted' : '') }}"
                    id="task-row-{{ $task->id }}">
                    <td class="text-center ps-2">
                        <button type="button"
                                class="btn btn-sm urgent-btn border-0 p-0"
                                style="font-size:1.1rem;line-height:1;background:none;"
                                onclick="toggleUrgent({{ $task->id }}, this)"
                                title="{{ $task->is_urgent ? 'Remove urgent flag' : 'Mark as urgent' }}">
                            <i class="bi bi-fire{{ $task->is_urgent ? ' text-danger' : ' text-muted opacity-25' }}"></i>
                        </button>
                    </td>
                    <td>
                        <div class="{{ $task->status === 'completed' ? 'text-decoration-line-through text-muted' : 'fw-semibold' }}">
                            {{ $task->name }}
                            @if($task->is_urgent)
                                <span class="badge ms-1" style="background:#e85d04;font-size:.65rem;">URGENT</span>
                            @endif
                        </div>
                        @if($task->description)<small class="text-muted">{{ Str::limit($task->description, 60) }}</small>@endif
                    </td>
                    <td><span class="small text-muted">{{ $task->assignedTo?->name ?? '—' }}</span></td>
                    <td class="text-center"><span class="badge bg-{{ $task->priority_badge }}">{{ $task->priority }}</span></td>
                    <td class="text-center"><span class="badge bg-{{ $task->status_badge }}">{{ ucfirst(str_replace('_',' ',$task->status)) }}</span></td>
                    <td class="{{ $task->due_date && $task->due_date->isPast() && $task->status !== 'completed' ? 'text-danger fw-semibold' : '' }}">
                        {{ $task->due_date ? $task->due_date->format('d M Y') : '—' }}
                    </td>
                    <td class="text-end">
                        <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                        @can('manager')
                        <form method="POST" action="{{ route('tasks.destroy', $task) }}" class="d-inline" onsubmit="return confirm('Delete task?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No tasks found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white border-top">
        @include('partials.pagination', ['paginator' => $tasks])
    </div>
</div>
@endsection

@push('styles')
<style>
    .urgent-row td { background: #fff4ee !important; border-left: 3px solid #e85d04; }
    .urgent-row td:first-child { border-left: 3px solid #e85d04; }
</style>
@endpush

@push('scripts')
<script>
document.querySelectorAll('.auto-filter').forEach(el => el.addEventListener('change', () => el.closest('form').submit()));

async function toggleUrgent(taskId, btn) {
    btn.disabled = true;
    try {
        const res  = await fetch(`/tasks/${taskId}/urgent`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
        });
        const data = await res.json();
        if (res.ok) {
            const icon = btn.querySelector('i');
            const row  = document.getElementById(`task-row-${taskId}`);
            if (data.is_urgent) {
                icon.className = 'bi bi-fire text-danger';
                btn.title = 'Remove urgent flag';
                row.classList.add('urgent-row');
                // Add/update URGENT badge in task name cell
                const nameDiv = row.querySelector('td:nth-child(2) div');
                if (!nameDiv.querySelector('.urgent-badge')) {
                    const badge = document.createElement('span');
                    badge.className = 'badge ms-1 urgent-badge';
                    badge.style.cssText = 'background:#e85d04;font-size:.65rem;';
                    badge.textContent = 'URGENT';
                    nameDiv.appendChild(badge);
                }
            } else {
                icon.className = 'bi bi-fire text-muted opacity-25';
                btn.title = 'Mark as urgent';
                row.classList.remove('urgent-row');
                const badge = row.querySelector('.urgent-badge');
                if (badge) badge.remove();
            }
        }
    } finally {
        btn.disabled = false;
    }
}
</script>
@endpush
