@extends('layouts.app')

@section('title', 'Projects')
@section('page-title', 'Projects')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Projects</h4>
    <a href="{{ route('projects.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New Project</a>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-4">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search projects or clients...">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    @foreach(['quote','active','on_hold','completed','cancelled'] as $s)
                        <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="client_id" class="form-select">
                    <option value="">All Clients</option>
                    @foreach($clients as $id => $name)
                        <option value="{{ $id }}" @selected(request('client_id') == $id)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-outline-secondary flex-grow-1">Filter</button>
                <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Project</th>
                    <th>Client</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Tasks</th>
                    <th>Budget</th>
                    <th>End Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($projects as $project)
                <tr>
                    <td><a href="{{ route('projects.show', $project) }}" class="fw-semibold text-decoration-none">{{ $project->name }}</a></td>
                    <td><a href="{{ route('clients.show', $project->client) }}" class="text-decoration-none text-muted">{{ $project->client->company_name }}</a></td>
                    <td class="text-center"><span class="badge bg-{{ $project->status_badge }}">{{ ucfirst(str_replace('_',' ',$project->status)) }}</span></td>
                    <td class="text-center"><span class="badge bg-light text-dark">{{ $project->tasks_count }}</span></td>
                    <td>{{ $project->budget ? '£'.number_format($project->budget, 0) : '—' }}</td>
                    <td>{{ $project->end_date ? $project->end_date->format('d M Y') : '—' }}</td>
                    <td class="text-end">
                        <a href="{{ route('projects.edit', $project) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                        @can('manager')
                        <form method="POST" action="{{ route('projects.destroy', $project) }}" class="d-inline" onsubmit="return confirm('Delete this project?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No projects found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($projects->hasPages())
        <div class="card-footer bg-white">{{ $projects->links() }}</div>
    @endif
</div>
@endsection
