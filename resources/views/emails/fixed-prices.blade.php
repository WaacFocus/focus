<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Billing</title>
</head>
<body style="margin:0;padding:0;background:#f0f4f4;font-family:Arial,Helvetica,sans-serif;color:#1a1a1a;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f4f4;padding:24px 0;">
  <tr>
    <td align="center">
      <table width="680" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.08);">

        {{-- Header --}}
        <tr>
          <td style="background:#0C3D38;padding:24px 32px;">
            <table width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td style="vertical-align:middle;">
                  <img src="{{ asset('images/woods-logo.png') }}" alt="Logo" style="height:40px;display:block;">
                </td>
                <td style="text-align:right;vertical-align:middle;">
                  <h1 style="margin:0 0 4px;font-size:22px;color:#ffffff;font-weight:700;">Billing</h1>
                  <p style="margin:0;font-size:12px;color:rgba(255,255,255,.65);">Generated {{ now()->format('d F Y') }}</p>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        {{-- Summary row --}}
        <tr>
          <td style="padding:20px 32px;">
            <table width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td width="30%" style="text-align:center;background:#f0fdfa;border-radius:6px;padding:14px 8px;">
                  <div style="font-size:20px;font-weight:700;color:#17B4A7;line-height:1;">£{{ number_format($metrics['monthly'], 2) }}</div>
                  <div style="font-size:11px;color:#666;margin-top:4px;">Monthly Fees</div>
                  <div style="font-size:10px;color:#999;">per month</div>
                </td>
                <td width="3%"></td>
                <td width="30%" style="text-align:center;background:#f0fdf4;border-radius:6px;padding:14px 8px;">
                  <div style="font-size:20px;font-weight:700;color:#16a34a;line-height:1;">£{{ number_format($metrics['annual'], 2) }}</div>
                  <div style="font-size:11px;color:#666;margin-top:4px;">Annual Fees</div>
                  <div style="font-size:10px;color:#999;">per year</div>
                </td>
                <td width="3%"></td>
                <td width="34%" style="text-align:center;background:#0C3D38;border-radius:6px;padding:14px 8px;">
                  <div style="font-size:20px;font-weight:700;color:#ffffff;line-height:1;">£{{ number_format($metrics['grf'], 2) }}</div>
                  <div style="font-size:11px;color:rgba(255,255,255,.75);margin-top:4px;">GRF</div>
                  <div style="font-size:10px;color:rgba(255,255,255,.5);">Gross Recurring Fees</div>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        {{-- Monthly Fees Breakdown --}}
        <tr>
          <td style="padding:0 32px 8px;">
            <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin-bottom:24px;">
              <thead>
                <tr style="background:#0C3D38;">
                  <th colspan="2" style="text-align:left;padding:8px 10px;color:#ffffff;font-size:12px;font-weight:700;">Monthly Fees Breakdown</th>
                  <th style="text-align:right;padding:8px 10px;color:rgba(255,255,255,.6);font-size:11px;font-weight:normal;">{{ $clients->count() }} clients</th>
                </tr>
                <tr style="background:#f8fafb;">
                  <th style="text-align:left;padding:8px 10px;border-bottom:2px solid #e2e8f0;color:#475569;font-weight:600;width:40%;">Client</th>
                  <th style="text-align:left;padding:8px 10px;border-bottom:2px solid #e2e8f0;color:#475569;font-weight:600;width:20%;">Code</th>
                  <th style="text-align:right;padding:8px 10px;border-bottom:2px solid #e2e8f0;color:#475569;font-weight:600;width:40%;">Amount</th>
                </tr>
              </thead>
              <tbody>
                @forelse($clients as $client)
                <tr>
                  <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;font-weight:600;">{{ $client->company_name }}</td>
                  <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;color:#9ca3af;font-size:11px;">{{ $client->client_code ?: '—' }}</td>
                  <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:right;">{{ $client->fpa_amount ? '£'.number_format($client->fpa_amount, 2) : '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="3" style="padding:16px;text-align:center;color:#9ca3af;">No clients with fixed prices found.</td></tr>
                @endforelse
              </tbody>
              @if($clients->isNotEmpty())
              <tfoot>
                <tr style="background:#f8fafb;font-weight:700;">
                  <td colspan="2" style="padding:8px 10px;border-top:2px solid #e2e8f0;">Totals</td>
                  <td style="padding:8px 10px;border-top:2px solid #e2e8f0;text-align:right;">£{{ number_format($totalFpa, 2) }}</td>
                </tr>
              </tfoot>
              @endif
            </table>
          </td>
        </tr>

        {{-- Annual Fees Breakdown --}}
        @if($annualTotal > 0)
        <tr>
          <td style="padding:0 32px 8px;">
            <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin-bottom:24px;">
              <thead>
                <tr style="background:#0C3D38;">
                  <th colspan="3" style="text-align:left;padding:8px 10px;color:#ffffff;font-size:12px;font-weight:700;">Annual Fees Breakdown</th>
                  <th style="text-align:right;padding:8px 10px;color:rgba(255,255,255,.6);font-size:11px;font-weight:normal;">£{{ number_format($annualTotal, 2) }} / year</th>
                </tr>
                <tr style="background:#f8fafb;">
                  <th style="text-align:left;padding:8px 10px;border-bottom:2px solid #e2e8f0;color:#475569;font-weight:600;width:40%;">Client</th>
                  <th style="text-align:left;padding:8px 10px;border-bottom:2px solid #e2e8f0;color:#475569;font-weight:600;width:20%;">Code</th>
                  <th style="text-align:left;padding:8px 10px;border-bottom:2px solid #e2e8f0;color:#475569;font-weight:600;width:15%;">Description</th>
                  <th style="text-align:right;padding:8px 10px;border-bottom:2px solid #e2e8f0;color:#475569;font-weight:600;width:25%;">Amount</th>
                </tr>
              </thead>
              <tbody>
                @foreach($annualFpaClients as $c)
                <tr>
                  <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;font-weight:600;">{{ $c->company_name }}</td>
                  <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;color:#9ca3af;font-size:11px;">{{ $c->client_code ?: '—' }}</td>
                  <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;color:#9ca3af;font-size:11px;">Fixed Price Agreement</td>
                  <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:right;">£{{ number_format($c->fpa_amount, 2) }}</td>
                </tr>
                @endforeach
                @foreach($annualLines as $line)
                <tr>
                  <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;font-weight:600;">{{ $line->client->company_name }}</td>
                  <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;color:#9ca3af;font-size:11px;">{{ $line->client->client_code ?: '—' }}</td>
                  <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;color:#9ca3af;font-size:11px;">{{ $line->description ?: '—' }}</td>
                  <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:right;">£{{ number_format($line->amount, 2) }}</td>
                </tr>
                @endforeach
              </tbody>
              <tfoot>
                <tr style="background:#f8fafb;font-weight:700;">
                  <td colspan="3" style="padding:8px 10px;border-top:2px solid #e2e8f0;">Total</td>
                  <td style="padding:8px 10px;border-top:2px solid #e2e8f0;text-align:right;">£{{ number_format($annualTotal, 2) }}</td>
                </tr>
              </tfoot>
            </table>
          </td>
        </tr>
        @endif

        {{-- Footer --}}
        <tr>
          <td style="padding:20px 32px;border-top:1px solid #e2e8f0;">
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
