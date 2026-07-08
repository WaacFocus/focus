@extends('layouts.app')

@section('title', 'Services')
@section('page-title', 'Services')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Services</h4>
    <a href="{{ route('services.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New Service</a>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-8">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search services...">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-outline-secondary flex-grow-1">Filter</button>
                <a href="{{ route('services.index') }}" class="btn btn-outline-secondary">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Service Name</th>
                    <th>Description</th>
                    <th class="text-center">Clients</th>
                    <th class="text-center">Active</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($services as $service)
                <tr>
                    <td class="fw-semibold">{{ $service->name }}</td>
                    <td class="text-muted small">{{ Str::limit($service->description, 60) }}</td>
                    <td class="text-center"><span class="badge bg-light text-dark">{{ $service->clients_count }}</span></td>
                    <td class="text-center">
                        @if($service->is_active)
                            <i class="bi bi-check-circle-fill text-success"></i>
                        @else
                            <i class="bi bi-x-circle text-secondary"></i>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('services.edit', $service) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                        @can('manager')
                        <form method="POST" action="{{ route('services.destroy', $service) }}" class="d-inline" onsubmit="return confirm('Delete this service?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No services found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white border-top">
        @include('partials.pagination', ['paginator' => $services])
    </div>
</div>
@endsection
