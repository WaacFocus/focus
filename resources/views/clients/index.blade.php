@extends('layouts.app')

@section('title', 'Clients')
@section('page-title', 'Clients')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Clients</h4>
    <button class="btn btn-primary" type="button" onclick="openClientPanel()">
        <i class="bi bi-plus-lg me-1"></i>New Client
    </button>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-6">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by name, contact or email...">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="active"   @selected(request('status') === 'active')>Active</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                    <option value="prospect" @selected(request('status') === 'prospect')>Prospect</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-outline-secondary flex-grow-1"><i class="bi bi-search me-1"></i>Filter</button>
                <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Company</th>
                    <th>Type</th>
                    <th>Contact</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th class="text-center">Projects</th>
                    <th class="text-center">Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $client)
                <tr>
                    <td><a href="{{ route('clients.show', $client) }}" class="fw-semibold text-decoration-none">{{ $client->company_name }}</a></td>
                    <td><span class="text-muted small">{{ $client->clientType?->name ?? '—' }}</span></td>
                    <td>{{ $client->contact_name }}</td>
                    <td><a href="mailto:{{ $client->email }}" class="text-decoration-none">{{ $client->email }}</a></td>
                    <td>{{ $client->phone }}</td>
                    <td class="text-center"><span class="badge bg-light text-dark">{{ $client->projects_count }}</span></td>
                    <td class="text-center"><span class="badge bg-{{ $client->status_badge }}">{{ ucfirst($client->status) }}</span></td>
                    <td class="text-end">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="openClientPanel({{ $client->id }})">
                            <i class="bi bi-pencil"></i>
                        </button>
                        @can('manager')
                        <form method="POST" action="{{ route('clients.destroy', $client) }}" class="d-inline" onsubmit="return confirm('Delete this client?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4">No clients found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($clients->hasPages())
        <div class="card-footer bg-white">{{ $clients->links() }}</div>
    @endif
</div>

@include('clients._panel')
@endsection
