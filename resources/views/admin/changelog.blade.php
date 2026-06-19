@extends('layouts.app')

@section('title', 'Version Log')
@section('page-title', 'Version Log')

@section('content')
<div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <h4 class="mb-1">Version Log</h4>
        <p class="text-muted small mb-0">Full history of changes, additions, and fixes across all versions.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap no-print">
        <a href="{{ route('changelog.pdf') }}" class="btn btn-sm btn-outline-danger">
            <i class="bi bi-file-earmark-pdf me-1"></i>Export PDF
        </a>
        <a href="{{ route('changelog.download') }}" class="btn btn-sm btn-outline-success">
            <i class="bi bi-file-earmark-arrow-down me-1"></i>Download .md
        </a>
        <button onclick="window.print()" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-printer me-1"></i>Print
        </button>
    </div>
</div>

<div class="changelog-body">
    {!! $html !!}
</div>

@push('styles')
<style>
.changelog-body h1 {
    display: none; /* title already shown in page header */
}
.changelog-body hr {
    border-color: #e9ecef;
    margin: 0.5rem 0 1.75rem;
}
.changelog-body h2 {
    font-size: 1.05rem;
    font-weight: 700;
    color: #fff;
    background: var(--brand-dark, #0C3D38);
    padding: 0.55rem 1rem;
    border-radius: 6px;
    margin: 0 0 0.75rem;
    letter-spacing: .01em;
}
.changelog-body h3 {
    font-size: 0.78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    margin: 1rem 0 0.35rem;
    padding-left: 0.25rem;
    border-left: 3px solid var(--brand-teal, #17B4A7);
    color: #444;
}
.changelog-body h3:first-of-type { margin-top: 0.5rem; }
.changelog-body ul {
    padding-left: 1.2rem;
    margin-bottom: 0.5rem;
}
.changelog-body ul li {
    font-size: 0.875rem;
    color: #333;
    margin-bottom: 0.3rem;
    line-height: 1.5;
}
.changelog-body ul li strong {
    color: #0C3D38;
}
.changelog-body ul li ul {
    margin-top: 0.2rem;
}
.changelog-body p {
    font-size: 0.875rem;
    color: #555;
}

/* Wrap each h2+content block in a card-like box */
.changelog-body h2 { margin-top: 1.75rem; }
.changelog-body h2:first-child { margin-top: 0; }

@media print {
    .no-print, .sidebar, .topbar, nav { display: none !important; }
    .changelog-body h2 { background: #0C3D38 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
}
</style>
@endpush
@endsection
