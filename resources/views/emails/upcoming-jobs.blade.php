<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Upcoming Jobs Report</title>
</head>
<body style="margin:0;padding:0;background:#f0f4f4;font-family:Arial,Helvetica,sans-serif;color:#1a1a1a;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f4f4;padding:24px 0;">
  <tr>
    <td align="center">
      <table width="680" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.08);">

        {{-- Header --}}
        <tr>
          <td style="background:#0C3D38;padding:28px 32px;">
            <p style="margin:0;font-size:11px;color:rgba(255,255,255,.5);letter-spacing:.06em;text-transform:uppercase;">Focus — Accounting Practice</p>
            <h1 style="margin:6px 0 4px;font-size:22px;color:#ffffff;font-weight:700;">Upcoming Jobs</h1>
            <p style="margin:0;font-size:13px;color:rgba(255,255,255,.65);">Non-completed jobs due on or before {{ now()->addDays(30)->format('d F Y') }} &nbsp;·&nbsp; Generated {{ now()->format('d F Y') }}</p>
          </td>
        </tr>

        {{-- Summary row --}}
        <tr>
          <td style="padding:20px 32px;">
            <table width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td width="32%" style="text-align:center;background:#fef2f2;border-radius:6px;padding:14px 8px;">
                  <div style="font-size:28px;font-weight:700;color:#dc2626;line-height:1;">{{ $overdueCount }}</div>
                  <div style="font-size:11px;color:#666;margin-top:4px;">Overdue</div>
                </td>
                <td width="4%"></td>
                <td width="32%" style="text-align:center;background:#fffbeb;border-radius:6px;padding:14px 8px;">
                  <div style="font-size:28px;font-weight:700;color:#d97706;line-height:1;">{{ $todayCount }}</div>
                  <div style="font-size:11px;color:#666;margin-top:4px;">Due Today</div>
                </td>
                <td width="4%"></td>
                <td width="32%" style="text-align:center;background:#f0fdf4;border-radius:6px;padding:14px 8px;">
                  <div style="font-size:28px;font-weight:700;color:#16a34a;line-height:1;">{{ $jobs->count() }}</div>
                  <div style="font-size:11px;color:#666;margin-top:4px;">Total Jobs</div>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        {{-- Jobs table --}}
        <tr>
          <td style="padding:0 32px 8px;">
            <p style="margin:0 0 10px;font-size:13px;font-weight:700;color:#0C3D38;text-transform:uppercase;letter-spacing:.04em;">All Upcoming Jobs</p>
            <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;font-size:13px;">
              <thead>
                <tr style="background:#f8fafb;">
                  <th style="text-align:left;padding:8px 10px;border-bottom:2px solid #e2e8f0;color:#475569;font-weight:600;">Job</th>
                  <th style="text-align:left;padding:8px 10px;border-bottom:2px solid #e2e8f0;color:#475569;font-weight:600;">Client</th>
                  <th style="text-align:left;padding:8px 10px;border-bottom:2px solid #e2e8f0;color:#475569;font-weight:600;">Assigned To</th>
                  <th style="text-align:left;padding:8px 10px;border-bottom:2px solid #e2e8f0;color:#475569;font-weight:600;">Due Date</th>
                  <th style="text-align:center;padding:8px 10px;border-bottom:2px solid #e2e8f0;color:#475569;font-weight:600;">Status</th>
                </tr>
              </thead>
              <tbody>
                @forelse($jobs as $job)
                @php
                  $isOverdue = $job->due_date->isPast() && !$job->due_date->isToday();
                  $isToday   = $job->due_date->isToday();
                  $rowBg     = $isOverdue ? '#fff5f5' : ($isToday ? '#fffdf0' : '#ffffff');
                @endphp
                <tr style="background:{{ $rowBg }};">
                  <td style="padding:8px 10px;border-bottom:1px solid #f1f5f9;font-weight:600;">{{ $job->name }}</td>
                  <td style="padding:8px 10px;border-bottom:1px solid #f1f5f9;color:#555;">{{ $job->client?->company_name ?? '—' }}</td>
                  <td style="padding:8px 10px;border-bottom:1px solid #f1f5f9;color:#555;">{{ $job->assignedTo->name }}</td>
                  <td style="padding:8px 10px;border-bottom:1px solid #f1f5f9;color:{{ $isOverdue ? '#dc2626' : '#374151' }};font-weight:{{ $isOverdue ? '600' : '400' }};">
                    {{ $job->due_date->format('d M Y') }}
                    @if($isOverdue) <span style="background:#dc2626;color:#fff;font-size:10px;padding:1px 5px;border-radius:3px;margin-left:4px;">Overdue</span>
                    @elseif($isToday) <span style="background:#d97706;color:#fff;font-size:10px;padding:1px 5px;border-radius:3px;margin-left:4px;">Today</span>
                    @endif
                  </td>
                  <td style="padding:8px 10px;border-bottom:1px solid #f1f5f9;text-align:center;">
                    <span style="background:#e2e8f0;color:#475569;font-size:11px;padding:2px 8px;border-radius:4px;">{{ ucfirst(str_replace('_', ' ', $job->status)) }}</span>
                  </td>
                </tr>
                @empty
                <tr><td colspan="5" style="padding:16px;text-align:center;color:#9ca3af;">No upcoming jobs found.</td></tr>
                @endforelse
              </tbody>
            </table>
          </td>
        </tr>

        {{-- By user breakdown --}}
        @if($byUser->isNotEmpty())
        <tr>
          <td style="padding:20px 32px 8px;">
            <p style="margin:0 0 10px;font-size:13px;font-weight:700;color:#0C3D38;text-transform:uppercase;letter-spacing:.04em;">By Team Member</p>
            <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;font-size:13px;">
              <thead>
                <tr style="background:#f8fafb;">
                  <th style="text-align:left;padding:8px 10px;border-bottom:2px solid #e2e8f0;color:#475569;font-weight:600;">Team Member</th>
                  <th style="text-align:center;padding:8px 10px;border-bottom:2px solid #e2e8f0;color:#475569;font-weight:600;">Overdue</th>
                  <th style="text-align:center;padding:8px 10px;border-bottom:2px solid #e2e8f0;color:#475569;font-weight:600;">Due Today</th>
                  <th style="text-align:center;padding:8px 10px;border-bottom:2px solid #e2e8f0;color:#475569;font-weight:600;">Total</th>
                </tr>
              </thead>
              <tbody>
                @foreach($byUser->sortKeys() as $name => $userJobs)
                @php
                  $oc = $userJobs->filter(fn($j) => $j->due_date->isPast() && !$j->due_date->isToday())->count();
                  $tc = $userJobs->filter(fn($j) => $j->due_date->isToday())->count();
                @endphp
                <tr style="background:#ffffff;">
                  <td style="padding:8px 10px;border-bottom:1px solid #f1f5f9;font-weight:600;">{{ $name }}</td>
                  <td style="padding:8px 10px;border-bottom:1px solid #f1f5f9;text-align:center;">
                    @if($oc) <span style="background:#dc2626;color:#fff;font-size:11px;padding:2px 8px;border-radius:4px;">{{ $oc }}</span>
                    @else <span style="color:#9ca3af;">—</span> @endif
                  </td>
                  <td style="padding:8px 10px;border-bottom:1px solid #f1f5f9;text-align:center;">
                    @if($tc) <span style="background:#d97706;color:#fff;font-size:11px;padding:2px 8px;border-radius:4px;">{{ $tc }}</span>
                    @else <span style="color:#9ca3af;">—</span> @endif
                  </td>
                  <td style="padding:8px 10px;border-bottom:1px solid #f1f5f9;text-align:center;font-weight:700;">{{ $userJobs->count() }}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </td>
        </tr>
        @endif

        {{-- Footer --}}
        <tr>
          <td style="padding:20px 32px;border-top:1px solid #e2e8f0;margin-top:12px;">
            <p style="margin:0;font-size:11px;color:#9ca3af;text-align:center;">
              This report was sent from Focus &nbsp;·&nbsp; {{ now()->format('d F Y, H:i') }}
            </p>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>

</body>
</html>
