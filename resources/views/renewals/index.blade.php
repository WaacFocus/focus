@extends('layouts.app')

@section('title', 'Engagement Letters')
@section('page-title', 'Engagement Letters')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Engagement Letters</h4>
    <a href="{{ route('renewals.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New Letter</a>
</div>

<div class="mb-3">
    <div class="btn-group">
        <a href="{{ route('renewals.index', array_merge(request()->except('filter'), ['filter' => 'upcoming'])) }}"
            class="btn btn-sm {{ $filter === 'upcoming' ? 'btn-primary' : 'btn-outline-secondary' }}">
            Due (90 days)
        </a>
        <a href="{{ route('renewals.index', array_merge(request()->except('filter'), ['filter' => 'overdue'])) }}"
            class="btn btn-sm {{ $filter === 'overdue' ? 'btn-danger' : 'btn-outline-secondary' }}">
            Overdue
        </a>
        <a href="{{ route('renewals.index', array_merge(request()->except('filter'), ['filter' => 'all'])) }}"
            class="btn btn-sm {{ $filter === 'all' ? 'btn-secondary' : 'btn-outline-secondary' }}">
            All
        </a>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <input type="hidden" name="filter" value="{{ $filter }}">
            <div class="col-md-4">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search letter type or client...">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    @foreach(['pending' => 'Pending','sent' => 'Sent','signed' => 'Signed','overdue' => 'Overdue'] as $val => $label)
                        <option value="{{ $val }}" @selected(request('status') === $val)>{{ $label }}</option>
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
                <a href="{{ route('renewals.index') }}" class="btn btn-outline-secondary">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Client</th>
                    <th>Letter Type</th>
                    <th>Last Completed</th>
                    <th>Next Due</th>
                    <th class="text-center">Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($renewals as $renewal)
                <tr>
                    <td><a href="{{ route('clients.show', $renewal->client) }}" class="text-decoration-none fw-medium">{{ $renewal->client->company_name }}</a></td>
                    <td>{{ $renewal->description }}</td>
                    <td class="text-muted small">{{ $renewal->completed_date ? $renewal->completed_date->format('d M Y') : '—' }}</td>
                    <td class="{{ $renewal->is_overdue ? 'text-danger fw-semibold' : '' }}">
                        {{ $renewal->due_date ? $renewal->due_date->format('d M Y') : '—' }}
                        @if($renewal->is_overdue) <span class="badge bg-danger ms-1">Overdue</span>@endif
                    </td>
                    <td class="text-center"><span class="badge bg-{{ $renewal->status_badge }}">{{ ucfirst($renewal->status) }}</span></td>
                    <td class="text-end">
                        <a href="{{ route('renewals.edit', $renewal) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                        @can('manager')
                        <form method="POST" action="{{ route('renewals.destroy', $renewal) }}" class="d-inline" onsubmit="return confirm('Delete this engagement letter?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No engagement letters found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($renewals->hasPages())
        <div class="card-footer bg-white">{{ $renewals->links() }}</div>
    @endif
</div>
@endsection
