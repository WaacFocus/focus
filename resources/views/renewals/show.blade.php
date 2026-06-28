@extends('layouts.app')

@section('title', 'Engagement Letter')
@section('page-title', 'Engagement Letters')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">{{ $renewal->description }}</h4>
    <a href="{{ route('renewals.edit', $renewal) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
</div>

<div class="card shadow-sm" style="max-width:600px">
    <div class="card-body">
        <div class="mb-2"><small class="text-muted">Client:</small> <a href="{{ route('clients.show', $renewal->client) }}">{{ $renewal->client->company_name }}</a></div>
        <div class="mb-2"><small class="text-muted">Letter Type:</small> {{ $renewal->description }}</div>
        @if($renewal->completed_date)
            <div class="mb-2"><small class="text-muted">Last Completed:</small> {{ $renewal->completed_date->format('d M Y') }}</div>
        @endif
        @if($renewal->due_date)
            <div class="mb-2">
                <small class="text-muted">Next Due:</small>
                <span class="{{ $renewal->is_overdue ? 'text-danger fw-semibold' : '' }}">{{ $renewal->due_date->format('d M Y') }}</span>
                @if($renewal->is_overdue) <span class="badge bg-danger ms-1">Overdue</span>@endif
            </div>
        @endif
        <div class="mb-2"><small class="text-muted">Status:</small> <span class="badge bg-{{ $renewal->status_badge }}">{{ ucfirst($renewal->status) }}</span></div>
        @if($renewal->notes)<p class="text-muted mt-2">{{ $renewal->notes }}</p>@endif
    </div>
</div>
@endsection
