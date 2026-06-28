<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Engagement Letter Signed</title>
</head>
<body style="margin:0;padding:0;background:#f4f7f6;font-family:Arial,Helvetica,sans-serif;color:#1a1a1a;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f7f6;padding:24px 0;">
  <tr><td align="center">
    <table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.1);">

      <tr><td style="background:#17B4A7;padding:24px 32px;">
        <p style="margin:0;font-size:11px;color:rgba(255,255,255,.65);letter-spacing:.06em;text-transform:uppercase;">Focus — Notification</p>
        <h1 style="margin:6px 0 4px;font-size:20px;color:#fff;font-weight:700;">Engagement Letter Signed</h1>
      </td></tr>

      <tr><td style="padding:28px 32px;">
        <p style="margin:0 0 20px;font-size:15px;">
          <strong>{{ $letter->client->company_name }}</strong> has signed their engagement letter.
        </p>

        <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafb;border-radius:6px;margin:0 0 20px;">
          <tr><td style="padding:16px 20px;">
            <table width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td style="padding:4px 0;font-size:13px;color:#555;width:120px;">Client</td>
                <td style="padding:4px 0;font-size:13px;font-weight:600;">{{ $letter->client->company_name }}</td>
              </tr>
              <tr>
                <td style="padding:4px 0;font-size:13px;color:#555;">Signed by</td>
                <td style="padding:4px 0;font-size:13px;">{{ $letter->signed_name }}</td>
              </tr>
              <tr>
                <td style="padding:4px 0;font-size:13px;color:#555;">Date &amp; Time</td>
                <td style="padding:4px 0;font-size:13px;">{{ $letter->signed_at->format('d F Y \a\t H:i') }}</td>
              </tr>
              <tr>
                <td style="padding:4px 0;font-size:13px;color:#555;">IP Address</td>
                <td style="padding:4px 0;font-size:13px;font-family:monospace;">{{ $letter->signed_ip }}</td>
              </tr>
              @if($letter->renewal)
              <tr>
                <td style="padding:4px 0;font-size:13px;color:#555;">Renewal</td>
                <td style="padding:4px 0;font-size:13px;">{{ $letter->renewal->description }} — updated to Signed</td>
              </tr>
              @endif
            </table>
          </td></tr>
        </table>

        <a href="{{ route('engagement-letters.show', $letter) }}"
           style="display:inline-block;background:#0C3D38;color:#fff;text-decoration:none;padding:10px 24px;border-radius:6px;font-size:14px;">
          View in Focus
        </a>
      </td></tr>

      <tr><td style="padding:16px 32px;border-top:1px solid #e2e8f0;">
        <p style="margin:0;font-size:11px;color:#aaa;text-align:center;">Focus &nbsp;·&nbsp; {{ now()->format('d F Y, H:i') }}</p>
      </td></tr>

    </table>
  </td></tr>
</table>
</body>
</html>
