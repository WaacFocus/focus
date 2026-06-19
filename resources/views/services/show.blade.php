@extends('layouts.app')

@section('title', $service->name)
@section('page-title', $service->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">{{ $service->name }}</h4>
    <a href="{{ route('services.edit', $service) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="mb-2"><small class="text-muted">Price:</small> <strong>£{{ number_format($service->default_price, 2) }}</strong> / {{ $service->billing_cycle_label }}</div>
                <div class="mb-2"><small class="text-muted">Status:</small> {{ $service->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>' }}</div>
                @if($service->description)<p class="text-muted mt-2">{{ $service->description }}</p>@endif
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-semibold">Assigned Clients ({{ $service->clients->count() }})</div>
            @if($service->clients->isNotEmpty())
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead class="table-light"><tr><th>Client</th><th class="text-end">Price</th><th>Since</th></tr></thead>
                    <tbody>
                        @foreach($service->clients as $client)
                        <tr>
                            <td><a href="{{ route('clients.show', $client) }}" class="text-decoration-none">{{ $client->company_name }}</a></td>
                            <td class="text-end">£{{ number_format($client->pivot->price_override ?? $service->default_price, 2) }}</td>
                            <td>{{ $client->pivot->start_date ? \Carbon\Carbon::parse($client->pivot->start_date)->format('d M Y') : '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="card-body text-muted small">No clients assigned to this service.</div>
            @endif
        </div>
    </div>
</div>
@endsection
