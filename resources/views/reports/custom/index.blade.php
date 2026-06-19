@extends('layouts.app')

@section('title', 'Saved Reports')
@section('page-title', 'Saved Reports')

@section('content')
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
    <div>
        <h4 class="mb-1">Saved Reports</h4>
        <p class="text-muted small mb-0">Your custom report library</p>
    </div>
    <a href="{{ route('reports.custom.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> New Report
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($savedReports->isEmpty())
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="bi bi-bar-chart-line fs-1 text-muted opacity-50"></i>
            <p class="mt-3 text-muted">No saved reports yet.</p>
            <a href="{{ route('reports.custom.create') }}" class="btn btn-primary">Build your first report</a>
        </div>
    </div>
@else
    <div class="row g-3">
        @foreach($savedReports as $report)
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between gap-2">
                        <div>
                            <h6 class="fw-semibold mb-1">{{ $report->name }}</h6>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                {{ ucfirst($report->config['source'] ?? '—') }}
                            </span>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light border-0" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('reports.custom.run', $report) }}">
                                        <i class="bi bi-play me-2"></i>Run
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('reports.custom.edit', $report) }}">
                                        <i class="bi bi-pencil me-2"></i>Edit
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('reports.custom.destroy', $report) }}"
                                          onsubmit="return confirm('Delete this report?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-trash me-2"></i>Delete
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <p class="text-muted small mt-2 mb-0">
                        {{ count($report->config['columns'] ?? []) }} column(s)
                        @if(!empty($report->config['filters']))
                            &middot; {{ count($report->config['filters']) }} filter(s)
                        @endif
                    </p>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-0">
                    <a href="{{ route('reports.custom.run', $report) }}" class="btn btn-sm btn-outline-primary w-100">
                        <i class="bi bi-play me-1"></i> Run Report
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endif
@endsection
