<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Signed Engagement Letter</title>
</head>
<body style="margin:0;padding:0;background:#f4f7f6;font-family:Arial,Helvetica,sans-serif;color:#1a1a1a;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f7f6;padding:24px 0;">
  <tr><td align="center">
    <table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.1);">

      <tr><td style="background:#0C3D38;padding:28px 32px;">
        <p style="margin:0;font-size:11px;color:rgba(255,255,255,.55);letter-spacing:.06em;text-transform:uppercase;">Engagement Letter — Signed</p>
        <h1 style="margin:6px 0 4px;font-size:22px;color:#fff;font-weight:700;">Your Signed Copy</h1>
        <p style="margin:0;font-size:13px;color:rgba(255,255,255,.65);">{{ $letter->client->company_name }}</p>
      </td></tr>

      <tr><td style="padding:28px 32px;">
        <p style="margin:0 0 16px;">Dear {{ $letter->client->contact_name ?: $letter->client->company_name }},</p>
        <p style="margin:0 0 16px;">Thank you for signing your engagement letter. Please find your signed copy attached to this email for your records.</p>

        <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafb;border-radius:6px;margin:20px 0;">
          <tr><td style="padding:16px 20px;">
            <p style="margin:0 0 6px;font-size:12px;text-transform:uppercase;color:#888;letter-spacing:.04em;">Signing Details</p>
            <table width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td style="padding:4px 0;font-size:13px;color:#555;width:120px;">Signed by</td>
                <td style="padding:4px 0;font-size:13px;font-weight:600;">{{ $letter->signed_name }}</td>
              </tr>
              <tr>
                <td style="padding:4px 0;font-size:13px;color:#555;">Date &amp; Time</td>
                <td style="padding:4px 0;font-size:13px;">{{ $letter->signed_at->format('d F Y \a\t H:i') }}</td>
              </tr>
              <tr>
                <td style="padding:4px 0;font-size:13px;color:#555;">IP Address</td>
                <td style="padding:4px 0;font-size:13px;font-family:monospace;">{{ $letter->signed_ip }}</td>
              </tr>
            </table>
          </td></tr>
        </table>

        <p style="margin:0;font-size:13px;color:#555;">Please keep this email and the attached PDF for your records. If you have any questions, please contact us.</p>
      </td></tr>

      <tr><td style="padding:16px 32px;border-top:1px solid #e2e8f0;">
        <p style="margin:0;font-size:11px;color:#aaa;text-align:center;">{{ now()->format('d F Y') }}</p>
      </td></tr>

    </table>
  </td></tr>
</table>
</body>
</html>
