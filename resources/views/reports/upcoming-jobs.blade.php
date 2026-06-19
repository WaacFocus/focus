@extends('layouts.app')

@section('title', 'Upcoming Jobs — Next 30 Days')
@section('page-title', 'Reports')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('reports.index') }}" class="text-muted text-decoration-none small no-print">
            <i class="bi bi-arrow-left me-1"></i>Reports
        </a>
        <h4 class="mb-0 mt-1">Upcoming Jobs — Next 30 Days</h4>
        <small class="text-muted">Non-completed jobs due on or before {{ now()->addDays(30)->format('d F Y') }}</small>
    </div>
    @include('reports._actions', [
        'csvUrl'          => route('reports.upcoming-jobs.csv'),
        'pdfPortraitUrl'  => route('reports.upcoming-jobs.pdf', 'portrait'),
        'pdfLandscapeUrl' => route('reports.upcoming-jobs.pdf', 'landscape'),
    ])
</div>

{{-- Summary cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm border-0 border-start border-danger border-3">
            <div class="card-body">
                <div class="text-muted small mb-1">Overdue</div>
                <div class="fs-4 fw-bold text-danger">{{ $overdueCount }}</div>
                <div class="text-muted small">Past due, not completed</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 border-start border-warning border-3">
            <div class="card-body">
                <div class="text-muted small mb-1">Due Today</div>
                <div class="fs-4 fw-bold text-warning">{{ $todayCount }}</div>
                <div class="text-muted small">{{ now()->format('d F Y') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 border-start border-primary border-3">
            <div class="card-body">
                <div class="text-muted small mb-1">Upcoming</div>
                <div class="fs-4 fw-bold text-primary">{{ $upcomingCount }}</div>
                <div class="text-muted small">Due in next 30 days</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0" style="background:var(--brand-dark,#0C3D38);">
            <div class="card-body">
                <div class="text-white-50 small mb-1">Total</div>
                <div class="fs-4 fw-bold text-white">{{ $jobs->count() }}</div>
                <div class="text-white-50 small">Jobs requiring action</div>
            </div>
        </div>
    </div>
</div>

{{-- Main table --}}
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
        <span>All Upcoming Jobs</span>
        <span class="text-muted small fw-normal">Sorted by due date</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Job</th>
                    <th>Client</th>
                    <th>Assigned To</th>
                    <th>Frequency</th>
                    <th>Due Date</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jobs as $job)
                <tr class="{{ $job->due_date->isPast() && !$job->due_date->isToday() ? 'table-danger' : ($job->due_date->isToday() ? 'table-warning' : '') }}">
                    <td class="fw-semibold">{{ $job->name }}</td>
                    <td>{{ $job->client?->company_name ?? '—' }}</td>
                    <td>{{ $job->assignedTo->name }}</td>
                    <td><span class="badge bg-light text-dark">{{ $job->frequency_label }}</span></td>
                    <td class="{{ $job->due_date->isPast() && !$job->due_date->isToday() ? 'text-danger fw-semibold' : '' }}">
                        {{ $job->due_date->format('d M Y') }}
                        @if($job->due_date->isPast() && !$job->due_date->isToday())
                            <span class="badge bg-danger ms-1">Overdue</span>
                        @elseif($job->due_date->isToday())
                            <span class="badge bg-warning text-dark ms-1">Today</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge bg-{{ $job->status_badge }}">{{ ucfirst(str_replace('_', ' ', $job->status)) }}</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No jobs due in the next 30 days.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Breakdown by user --}}
@if($byUser->isNotEmpty())
<div class="card shadow-sm">
    <div class="card-header bg-white fw-semibold">Breakdown by Team Member</div>
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Team Member</th>
                    <th class="text-center">Overdue</th>
                    <th class="text-center">Due Today</th>
                    <th class="text-center">Upcoming</th>
                    <th class="text-center fw-semibold">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($byUser->sortKeys() as $name => $userJobs)
                <tr>
                    <td class="fw-semibold">{{ $name }}</td>
                    <td class="text-center">
                        @php $oc = $userJobs->filter(fn($j) => $j->due_date->isPast() && !$j->due_date->isToday())->count(); @endphp
                        @if($oc) <span class="badge bg-danger">{{ $oc }}</span> @else <span class="text-muted">—</span> @endif
                    </td>
                    <td class="text-center">
                        @php $tc = $userJobs->filter(fn($j) => $j->due_date->isToday())->count(); @endphp
                        @if($tc) <span class="badge bg-warning text-dark">{{ $tc }}</span> @else <span class="text-muted">—</span> @endif
                    </td>
                    <td class="text-center">
                        @php $uc = $userJobs->filter(fn($j) => $j->due_date->isFuture())->count(); @endphp
                        <span class="text-muted">{{ $uc ?: '—' }}</span>
                    </td>
                    <td class="text-center fw-semibold">{{ $userJobs->count() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
