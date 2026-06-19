@extends('layouts.app')

@section('title', $project->name)
@section('page-title', $project->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">{{ $project->name }}</h4>
        <span class="badge bg-{{ $project->status_badge }}">{{ ucfirst(str_replace('_',' ',$project->status)) }}</span>
        <span class="text-muted ms-2">—</span>
        <a href="{{ route('clients.show', $project->client) }}" class="ms-2 text-decoration-none">{{ $project->client->company_name }}</a>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('tasks.create', ['project_id' => $project->id]) }}" class="btn btn-outline-primary"><i class="bi bi-plus-lg me-1"></i>Add Task</a>
        <a href="{{ route('projects.edit', $project) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold">Project Info</div>
            <div class="card-body">
                @if($project->description)
                    <p class="text-muted">{{ $project->description }}</p>
                @endif
                @if($project->start_date)
                    <div class="mb-2"><small class="text-muted">Start:</small> {{ $project->start_date->format('d M Y') }}</div>
                @endif
                @if($project->end_date)
                    <div class="mb-2"><small class="text-muted">End:</small> {{ $project->end_date->format('d M Y') }}</div>
                @endif
                @if($project->budget)
                    <div class="mb-2"><small class="text-muted">Budget:</small> £{{ number_format($project->budget, 2) }}</div>
                @endif
                @if($project->notes)
                    <hr>
                    <div class="small text-muted">{{ $project->notes }}</div>
                @endif
            </div>
        </div>

        @if($project->products->isNotEmpty())
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-semibold">Cost Breakdown</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr><th>Item</th><th class="text-end">Qty</th><th class="text-end">Price</th><th class="text-end">Total</th></tr>
                    </thead>
                    <tbody>
                        @foreach($project->products as $product)
                        <tr>
                            <td>{{ $product->name }}</td>
                            <td class="text-end">{{ $product->pivot->quantity }}</td>
                            <td class="text-end">£{{ number_format($product->pivot->unit_price, 2) }}</td>
                            <td class="text-end">£{{ number_format($product->pivot->quantity * $product->pivot->unit_price, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-light fw-bold">
                            <td colspan="3" class="text-end">Total:</td>
                            <td class="text-end">£{{ number_format($project->total_cost, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @if($project->budget)
            <div class="card-footer bg-white small">
                @php $margin = $project->budget - $project->total_cost; @endphp
                <span class="{{ $margin >= 0 ? 'text-success' : 'text-danger' }}">
                    Margin: £{{ number_format($margin, 2) }}
                </span>
            </div>
            @endif
        </div>
        @endif
    </div>

    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Tasks ({{ $project->tasks->count() }})</span>
                <a href="{{ route('tasks.create', ['project_id' => $project->id]) }}" class="btn btn-sm btn-outline-primary">Add Task</a>
            </div>
            @if($project->tasks->isNotEmpty())
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead class="table-light">
                        <tr><th>Task</th><th class="text-center">Priority</th><th class="text-center">Status</th><th>Due</th><th></th></tr>
                    </thead>
                    <tbody>
                        @foreach($project->tasks->sortBy(fn($t) => [$t->status === 'completed' ? 1 : 0, $t->due_date]) as $task)
                        <tr class="{{ $task->status === 'completed' ? 'text-muted' : '' }}">
                            <td>
                                <div class="{{ $task->status === 'completed' ? 'text-decoration-line-through' : 'fw-medium' }}">{{ $task->name }}</div>
                                @if($task->description)<small class="text-muted">{{ Str::limit($task->description, 60) }}</small>@endif
                            </td>
                            <td class="text-center"><span class="badge bg-{{ $task->priority_badge }}">{{ $task->priority }}</span></td>
                            <td class="text-center"><span class="badge bg-{{ $task->status_badge }}">{{ ucfirst(str_replace('_',' ',$task->status)) }}</span></td>
                            <td class="{{ $task->due_date && $task->due_date->isPast() && $task->status !== 'completed' ? 'text-danger' : '' }}">
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
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="card-body text-muted small">No tasks yet. <a href="{{ route('tasks.create', ['project_id' => $project->id]) }}">Add one.</a></div>
            @endif
        </div>
    </div>
</div>
@endsection
