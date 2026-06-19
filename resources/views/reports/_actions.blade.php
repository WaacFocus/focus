@php
    $csvUrl          ??= null;
    $pdfPortraitUrl  ??= null;
    $pdfLandscapeUrl ??= null;
@endphp

<div class="report-actions d-flex align-items-center gap-2 mb-4">
    <span class="text-muted small me-1">Export:</span>

    @if($csvUrl)
    <a href="{{ $csvUrl }}" class="btn btn-sm btn-outline-success">
        <i class="bi bi-file-earmark-spreadsheet me-1"></i>CSV
    </a>
    @endif

    @if($pdfPortraitUrl || $pdfLandscapeUrl)
    <div class="btn-group">
        <button type="button" class="btn btn-sm btn-outline-danger dropdown-toggle" data-bs-toggle="dropdown">
            <i class="bi bi-file-earmark-pdf me-1"></i>PDF
        </button>
        <ul class="dropdown-menu">
            @if($pdfPortraitUrl)
            <li>
                <a class="dropdown-item" href="{{ $pdfPortraitUrl }}">
                    <i class="bi bi-file-earmark-text me-2"></i>Portrait
                </a>
            </li>
            @endif
            @if($pdfLandscapeUrl)
            <li>
                <a class="dropdown-item" href="{{ $pdfLandscapeUrl }}">
                    <i class="bi bi-file-earmark-richtext me-2"></i>Landscape
                </a>
            </li>
            @endif
        </ul>
    </div>
    @endif

    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()">
        <i class="bi bi-printer me-1"></i>Print
    </button>
</div>
