<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    * { box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #222; margin: 0; padding: 20px; }
    h1  { font-size: 16px; margin: 0 0 2px; }
    .meta { font-size: 9px; color: #888; margin-bottom: 14px; }

    .summary-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
    .summary-table td { border: 1px solid #dee2e6; padding: 7px 10px; width: 25%; vertical-align: top; }
    .summary-table .lbl { font-size: 8px; text-transform: uppercase; color: #666; margin-bottom: 3px; }
    .summary-table .val { font-size: 14px; font-weight: bold; }
    .summary-table .sub { font-size: 8px; color: #888; margin-top: 2px; }
    .dark-cell { background: #0C3D38; color: #fff; }
    .dark-cell .lbl { color: rgba(255,255,255,.6); }
    .dark-cell .sub { color: rgba(255,255,255,.5); }

    h2 { font-size: 11px; text-transform: uppercase; letter-spacing: .04em; color: #666; margin: 0 0 6px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    thead th { background: #f0f1f3; padding: 5px 7px; text-align: left; font-size: 8px; text-transform: uppercase; letter-spacing: .03em; border-bottom: 2px solid #c8ccd0; }
    tbody td { padding: 5px 7px; border-bottom: 1px solid #e9ecef; font-size: 9px; }
    tbody tr:nth-child(even) td { background: #fafafa; }
    .overdue td { background: #fff5f5 !important; }
    .today td   { background: #fffbf0 !important; }
    .badge { display: inline-block; padding: 1px 5px; border-radius: 3px; font-size: 8px; }
    .badge-danger  { background: #dc3545; color: #fff; }
    .badge-warning { background: #ffc107; color: #222; }
    .badge-light   { background: #e9ecef; color: #444; }
    .badge-secondary { background: #6c757d; color: #fff; }
    .badge-primary   { background: #0d6efd; color: #fff; }
    .generated { font-size: 8px; color: #aaa; margin-top: 12px; }
</style>
</head>
<body>

<h1>Upcoming Jobs — Next 30 Days</h1>
<div class="meta">Non-completed jobs due on or before {{ now()->addDays(30)->format('d F Y') }} &nbsp;·&nbsp; Generated {{ now()->format('d F Y, H:i') }}</div>

@php
    $totalCount    = $jobs->count();
    $todayCount    = $jobs->filter(fn($j) => $j->due_date->isToday())->count();
    $upcomingCount = $jobs->filter(fn($j) => $j->due_date->isFuture())->count();
@endphp

<table class="summary-table">
    <tr>
        <td>
            <div class="lbl">Overdue</div>
            <div class="val" style="color:#dc3545;">{{ $overdueCount }}</div>
            <div class="sub">Past due, not completed</div>
        </td>
        <td>
            <div class="lbl">Due Today</div>
            <div class="val" style="color:#e6a800;">{{ $todayCount }}</div>
            <div class="sub">{{ now()->format('d F Y') }}</div>
        </td>
        <td>
            <div class="lbl">Upcoming</div>
            <div class="val" style="color:#0d6efd;">{{ $upcomingCount }}</div>
            <div class="sub">Due in next 30 days</div>
        </td>
        <td class="dark-cell">
            <div class="lbl">Total</div>
            <div class="val">{{ $totalCount }}</div>
            <div class="sub">Jobs requiring action</div>
        </td>
    </tr>
</table>

<h2>All Upcoming Jobs</h2>
<table>
    <thead>
        <tr>
            <th>Job</th>
            <th>Client</th>
            <th>Assigned To</th>
            <th>Frequency</th>
            <th>Due Date</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($jobs as $job)
        @php
            $rowClass = $job->due_date->isPast() && !$job->due_date->isToday() ? 'overdue' : ($job->due_date->isToday() ? 'today' : '');
        @endphp
        <tr class="{{ $rowClass }}">
            <td>{{ $job->name }}</td>
            <td>{{ $job->client?->company_name ?? '—' }}</td>
            <td>{{ $job->assignedTo->name }}</td>
            <td><span class="badge badge-light">{{ $job->frequency_label }}</span></td>
            <td>
                {{ $job->due_date->format('d M Y') }}
                @if($job->due_date->isPast() && !$job->due_date->isToday())
                    <span class="badge badge-danger">Overdue</span>
                @elseif($job->due_date->isToday())
                    <span class="badge badge-warning">Today</span>
                @endif
            </td>
            <td>
                @php
                    $bc = match($job->status) { 'pending' => 'badge-secondary', 'in_progress' => 'badge-primary', default => 'badge-secondary' };
                @endphp
                <span class="badge {{ $bc }}">{{ ucfirst(str_replace('_', ' ', $job->status)) }}</span>
            </td>
        </tr>
        @empty
        <tr><td colspan="6" style="text-align:center;color:#888;">No jobs due in the next 30 days.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="generated">Focus — {{ now()->format('d/m/Y H:i') }}</div>
</body>
</html>
