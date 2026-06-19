<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $savedReport->name }}</title>
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
            <h1 style="margin:6px 0 4px;font-size:22px;color:#ffffff;font-weight:700;">{{ $savedReport->name }}</h1>
            <p style="margin:0;font-size:13px;color:rgba(255,255,255,.65);">
                {{ ucfirst($savedReport->config['source'] ?? '') }}
                &nbsp;·&nbsp; {{ $result['count'] }} row{{ $result['count'] !== 1 ? 's' : '' }}
                &nbsp;·&nbsp; Generated {{ now()->format('d F Y') }}
            </p>
          </td>
        </tr>

        {{-- Table --}}
        <tr>
          <td style="padding:24px 32px 8px;">
            @if($result['count'] > 0)
            <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;font-size:13px;">
              <thead>
                <tr style="background:#f8fafb;">
                  @foreach($result['headers'] as $h)
                  <th style="text-align:left;padding:8px 10px;border-bottom:2px solid #e2e8f0;color:#475569;font-weight:600;">{{ $h }}</th>
                  @endforeach
                </tr>
              </thead>
              <tbody>
                @foreach($result['rows'] as $i => $row)
                <tr style="background:{{ $i % 2 === 0 ? '#ffffff' : '#f9fafb' }};">
                  @foreach($row as $cell)
                  <td style="padding:8px 10px;border-bottom:1px solid #f1f5f9;color:#374151;">{{ $cell }}</td>
                  @endforeach
                </tr>
                @endforeach
              </tbody>
            </table>
            @else
            <p style="color:#9ca3af;text-align:center;padding:24px 0;">No results found for this report.</p>
            @endif
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
