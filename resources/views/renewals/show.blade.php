@extends('layouts.app')

@section('title', 'Renewal')
@section('page-title', 'Renewal')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">{{ $renewal->description }}</h4>
    <a href="{{ route('renewals.edit', $renewal) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
</div>

<div class="card shadow-sm" style="max-width:600px">
    <div class="card-body">
        <div class="mb-2"><small class="text-muted">Client:</small> <a href="{{ route('clients.show', $renewal->client) }}">{{ $renewal->client->company_name }}</a></div>
        @if($renewal->service)
            <div class="mb-2"><small class="text-muted">Service:</small> {{ $renewal->service->name }}</div>
        @endif
        <div class="mb-2"><small class="text-muted">Due:</small> {{ $renewal->renewal_date->format('d M Y') }}</div>
        <div class="mb-2"><small class="text-muted">Amount:</small> {{ $renewal->amount ? '£'.number_format($renewal->amount, 2) : '—' }}</div>
        <div class="mb-2"><small class="text-muted">Cycle:</small> {{ ucfirst(str_replace('_',' ',$renewal->billing_cycle)) }}</div>
        <div class="mb-2"><small class="text-muted">Status:</small> <span class="badge bg-{{ $renewal->status_badge }}">{{ ucfirst($renewal->status) }}</span></div>
        @if($renewal->next_renewal_date)
            <div class="mb-2"><small class="text-muted">Next Renewal:</small> {{ $renewal->next_renewal_date->format('d M Y') }}</div>
        @endif
        @if($renewal->notes)<p class="text-muted mt-2">{{ $renewal->notes }}</p>@endif
    </div>
</div>
@endsection
