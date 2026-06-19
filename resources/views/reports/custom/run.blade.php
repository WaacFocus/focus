@extends('layouts.app')

@section('title', $savedReport->name)
@section('page-title', $savedReport->name)

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <a href="{{ route('reports.custom.index') }}" class="text-decoration-none text-muted small no-print">
            <i class="bi bi-arrow-left me-1"></i>Saved Reports
        </a>
        <h4 class="mb-1 mt-1">{{ $savedReport->name }}</h4>
        <p class="text-muted small mb-0">
            {{ ucfirst($savedReport->config['source'] ?? '') }}
            &middot; {{ $result['count'] }} result{{ $result['count'] !== 1 ? 's' : '' }}
            @if(!empty($savedReport->config['filters']))
                &middot; {{ count($savedReport->config['filters']) }} filter{{ count($savedReport->config['filters']) !== 1 ? 's' : '' }} applied
            @endif
        </p>
    </div>

    {{-- Actions bar --}}
    <div class="d-flex align-items-center gap-2 flex-wrap no-print">
        <a href="{{ route('reports.custom.edit', $savedReport) }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-pencil me-1"></i>Edit
        </a>

        <span class="text-muted small">Export:</span>

        <a href="{{ route('reports.custom.csv', $savedReport) }}" class="btn btn-sm btn-outline-success">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i>CSV
        </a>

        <div class="btn-group">
            <button type="button" class="btn btn-sm btn-outline-danger dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-file-earmark-pdf me-1"></i>PDF
            </button>
            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item" href="{{ route('reports.custom.pdf', [$savedReport, 'portrait']) }}">
                        <i class="bi bi-file-earmark-text me-2"></i>Portrait
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('reports.custom.pdf', [$savedReport, 'landscape']) }}">
                        <i class="bi bi-file-earmark-richtext me-2"></i>Landscape
                    </a>
                </li>
            </ul>
        </div>

        <button onclick="window.print()" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-printer me-1"></i>Print
        </button>

        @if($users->isNotEmpty())
        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#emailReportModal">
            <i class="bi bi-envelope me-1"></i>Email
        </button>
        @endif
    </div>
</div>

{{-- Results table --}}
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if($result['count'] > 0)
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        @foreach($result['headers'] as $h)
                        <th class="small fw-semibold">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($result['rows'] as $row)
                    <tr>
                        @foreach($row as $cell)
                        <td class="small align-middle">{{ $cell }}</td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center text-muted py-5">
            <i class="bi bi-inbox fs-1 opacity-25"></i>
            <p class="mt-3">No results found for this report.</p>
        </div>
        @endif
    </div>
</div>

{{-- Email modal --}}
@if($users->isNotEmpty())
<div class="modal fade" id="emailReportModal" tabindex="-1" aria-labelledby="emailReportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('reports.custom.email', $savedReport) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-semibold" id="emailReportModalLabel">
                        <i class="bi bi-envelope me-2 text-primary"></i>Email Report
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">Select the users to send this report to. Each will receive their own copy.</p>
                    <div class="mb-1">
                        <label class="form-label fw-semibold small">Send to</label>
                    </div>
                    @foreach($users as $user)
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox"
                               name="user_ids[]" value="{{ $user->id }}"
                               id="email_user_{{ $user->id }}">
                        <label class="form-check-label" for="email_user_{{ $user->id }}">
                            {{ $user->name }}
                            <span class="text-muted small ms-1">{{ $user->email }}</span>
                        </label>
                    </div>
                    @endforeach
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-send me-1"></i>Send Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('styles')
<style>
@media print {
    .no-print, .sidebar, .topbar, nav { display: none !important; }
    .card { box-shadow: none !important; border: 1px solid #dee2e6 !important; }
}
</style>
@endpush
@endsection
