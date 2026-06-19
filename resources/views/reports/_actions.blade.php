@php
    $csvUrl          ??= null;
    $pdfPortraitUrl  ??= null;
    $pdfLandscapeUrl ??= null;
    $reportType      ??= null;
    $users           ??= collect();
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

    @if($reportType && $users->isNotEmpty())
    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#emailReportModal">
        <i class="bi bi-envelope me-1"></i>Email
    </button>
    @endif
</div>

@if($reportType && $users->isNotEmpty())
<div class="modal fade" id="emailReportModal" tabindex="-1" aria-labelledby="emailReportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('reports.email') }}">
                @csrf
                <input type="hidden" name="report" value="{{ $reportType }}">

                <div class="modal-header">
                    <h5 class="modal-title fw-semibold" id="emailReportModalLabel">
                        <i class="bi bi-envelope me-2 text-primary"></i>Email Report
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        Select the users you'd like to send this report to. Each will receive their own copy.
                    </p>

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
