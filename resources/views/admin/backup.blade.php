@extends('layouts.app')

@section('title', 'Backup & Import')
@section('page-title', 'Backup & Import')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Backup & Import</h4>
        <p class="text-muted small mb-0">Export all data as CSV files or import records from a CSV.</p>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    @if(session('import_errors'))
        <hr class="my-2">
        <p class="mb-1 fw-semibold small">Row errors:</p>
        <ul class="mb-0 small">
            @foreach(session('import_errors') as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    @endif
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle me-2"></i>{{ $errors->first() }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-4">
    @foreach($schemas as $type => $schema)
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header text-white d-flex align-items-center justify-content-between" style="background:var(--brand-dark);">
                <span class="fw-semibold"><i class="bi {{ $schema['icon'] }} me-2"></i>{{ $schema['label'] }}</span>
                <span class="badge bg-white text-dark fw-normal">{{ number_format($counts[$type]) }} records</span>
            </div>
            <div class="card-body">

                {{-- Export --}}
                <p class="text-muted small fw-semibold text-uppercase mb-2" style="letter-spacing:.05em;">Export</p>
                <div class="d-flex gap-2 mb-4">
                    <a href="{{ route('backup.export', $type) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-file-earmark-arrow-down me-1"></i>Download CSV
                    </a>
                    <a href="{{ route('backup.template', $type) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-file-earmark-text me-1"></i>Example CSV
                    </a>
                </div>

                {{-- Import --}}
                <p class="text-muted small fw-semibold text-uppercase mb-2" style="letter-spacing:.05em;">Import</p>
                <form method="POST" action="{{ route('backup.import') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="type" value="{{ $type }}">
                    <div class="input-group input-group-sm">
                        <input type="file" name="file" class="form-control" accept=".csv,.txt" required>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-upload me-1"></i>Import
                        </button>
                    </div>
                </form>

                {{-- Notes --}}
                <div class="mt-3 p-2 rounded small text-muted" style="background:#f8f9fa;border:1px solid #e9ecef;line-height:1.5;">
                    <i class="bi bi-info-circle me-1"></i>{{ $schema['notes'] }}
                </div>

                {{-- Column list --}}
                <div class="mt-3">
                    <p class="text-muted small fw-semibold text-uppercase mb-1" style="letter-spacing:.05em;font-size:.68rem;">CSV Columns</p>
                    <div class="d-flex flex-wrap gap-1">
                        @foreach($schema['headers'] as $col)
                            <code class="small px-1 py-0 rounded" style="background:#e9ecef;color:#495057;font-size:.72rem;">{{ $col }}</code>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
