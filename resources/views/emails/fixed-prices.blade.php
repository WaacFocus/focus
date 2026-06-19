<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Fixed Price Summary</title>
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
            <h1 style="margin:6px 0 4px;font-size:22px;color:#ffffff;font-weight:700;">Fixed Price Summary</h1>
            <p style="margin:0;font-size:13px;color:rgba(255,255,255,.65);">All client FPA and payroll FPA amounts &nbsp;·&nbsp; Generated {{ now()->format('d F Y') }}</p>
          </td>
        </tr>

        {{-- Summary row --}}
        <tr>
          <td style="padding:20px 32px;">
            <table width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td width="32%" style="text-align:center;background:#f0fdfa;border-radius:6px;padding:14px 8px;">
                  <div style="font-size:22px;font-weight:700;color:#0C3D38;line-height:1;">£{{ number_format($totalFpa, 2) }}</div>
                  <div style="font-size:11px;color:#666;margin-top:4px;">Total FPA</div>
                  <div style="font-size:10px;color:#999;">{{ $clients->whereNotNull('fpa_amount')->count() }} clients</div>
                </td>
                <td width="4%"></td>
                <td width="32%" style="text-align:center;background:#f0fdf4;border-radius:6px;padding:14px 8px;">
                  <div style="font-size:22px;font-weight:700;color:#16a34a;line-height:1;">£{{ number_format($totalPayrollFpa, 2) }}</div>
                  <div style="font-size:11px;color:#666;margin-top:4px;">Total Payroll FPA</div>
                  <div style="font-size:10px;color:#999;">{{ $clients->whereNotNull('payroll_fpa')->count() }} clients</div>
                </td>
                <td width="4%"></td>
                <td width="32%" style="text-align:center;background:#0C3D38;border-radius:6px;padding:14px 8px;">
                  <div style="font-size:22px;font-weight:700;color:#ffffff;line-height:1;">£{{ number_format($grandTotal, 2) }}</div>
                  <div style="font-size:11px;color:rgba(255,255,255,.65);margin-top:4px;">Grand Total</div>
                  <div style="font-size:10px;color:rgba(255,255,255,.45);">{{ $clients->count() }} clients</div>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        {{-- Client table --}}
        <tr>
          <td style="padding:0 32px 8px;">
            <p style="margin:0 0 10px;font-size:13px;font-weight:700;color:#0C3D38;text-transform:uppercase;letter-spacing:.04em;">Client Breakdown</p>
            <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;font-size:12px;">
              <thead>
                <tr style="background:#f8fafb;">
                  <th style="text-align:left;padding:8px 10px;border-bottom:2px solid #e2e8f0;color:#475569;font-weight:600;">Client</th>
                  <th style="text-align:left;padding:8px 10px;border-bottom:2px solid #e2e8f0;color:#475569;font-weight:600;">Code</th>
                  <th style="text-align:right;padding:8px 10px;border-bottom:2px solid #e2e8f0;color:#475569;font-weight:600;">FPA</th>
                  <th style="text-align:left;padding:8px 10px;border-bottom:2px solid #e2e8f0;color:#475569;font-weight:600;">Interval</th>
                  <th style="text-align:right;padding:8px 10px;border-bottom:2px solid #e2e8f0;color:#475569;font-weight:600;">Payroll FPA</th>
                  <th style="text-align:right;padding:8px 10px;border-bottom:2px solid #e2e8f0;color:#475569;font-weight:600;">Total</th>
                </tr>
              </thead>
              <tbody>
                @forelse($clients as $client)
                <tr>
                  <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;font-weight:600;">{{ $client->company_name }}</td>
                  <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;color:#9ca3af;font-size:11px;">{{ $client->client_code ?: '—' }}</td>
                  <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:right;">{{ $client->fpa_amount ? '£'.number_format($client->fpa_amount, 2) : '—' }}</td>
                  <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;font-size:11px;color:#555;">{{ $client->billing_interval ? ucfirst($client->billing_interval) : '—' }}</td>
                  <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:right;">{{ $client->payroll_fpa ? '£'.number_format($client->payroll_fpa, 2) : '—' }}</td>
                  <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:right;font-weight:700;">
                    £{{ number_format(($client->fpa_amount ?? 0) + ($client->payroll_fpa ?? 0), 2) }}
                  </td>
                </tr>
                @empty
                <tr><td colspan="6" style="padding:16px;text-align:center;color:#9ca3af;">No clients with fixed prices found.</td></tr>
                @endforelse
              </tbody>
              @if($clients->isNotEmpty())
              <tfoot>
                <tr style="background:#f8fafb;font-weight:700;">
                  <td colspan="2" style="padding:8px 10px;border-top:2px solid #e2e8f0;">Totals</td>
                  <td style="padding:8px 10px;border-top:2px solid #e2e8f0;text-align:right;">£{{ number_format($totalFpa, 2) }}</td>
                  <td style="padding:8px 10px;border-top:2px solid #e2e8f0;"></td>
                  <td style="padding:8px 10px;border-top:2px solid #e2e8f0;text-align:right;">£{{ number_format($totalPayrollFpa, 2) }}</td>
                  <td style="padding:8px 10px;border-top:2px solid #e2e8f0;text-align:right;">£{{ number_format($grandTotal, 2) }}</td>
                </tr>
              </tfoot>
              @endif
            </table>
          </td>
        </tr>

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
