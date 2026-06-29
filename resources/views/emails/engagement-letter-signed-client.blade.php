<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Signed Engagement Letter</title>
</head>
<body style="margin:0;padding:0;background:#f0fafa;font-family:Arial,Helvetica,sans-serif;color:#1a1a1a;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0fafa;padding:24px 0;">
  <tr><td align="center">
    <table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.08);">

      {{-- Logo header --}}
      <tr><td style="background:#fff;padding:20px 32px 16px;border-bottom:3px solid #3DBFB8;">
        <img src="{{ asset('images/woods-logo.png') }}" alt="Woods Accounting & Consulting" width="160" height="36" style="width:160px;height:36px;display:block;border:0;">
      </td></tr>

      {{-- Teal banner --}}
      <tr><td style="background:#3DBFB8;padding:24px 32px;">
        <p style="margin:0;font-size:11px;color:#fff;letter-spacing:.08em;text-transform:uppercase;font-weight:600;">Engagement Letter — Signed</p>
        <h1 style="margin:6px 0 4px;font-size:22px;color:#fff;font-weight:700;">Your Signed Copy</h1>
        <p style="margin:0;font-size:14px;color:#fff;font-weight:600;">{{ $letter->client->company_name }}</p>
      </td></tr>

      {{-- Body --}}
      <tr><td style="padding:28px 32px;">
        <p style="margin:0 0 16px;">Dear {{ $letter->client->contact_first_name_greeting }},</p>
        <p style="margin:0 0 16px;">Thank you for signing your engagement letter. Please find your signed copy attached to this email for your records.</p>

        <table width="100%" cellpadding="0" cellspacing="0" style="border-radius:6px;margin:20px 0;border:1px solid #c8eeec;">
          <tr><td style="padding:16px 20px;background:#f0fafa;">
            <p style="margin:0 0 10px;font-size:11px;text-transform:uppercase;color:#3DBFB8;letter-spacing:.06em;font-weight:bold;">Signing Details</p>
            <table width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td style="padding:4px 0;font-size:13px;color:#888;width:120px;">Signed by</td>
                <td style="padding:4px 0;font-size:13px;font-weight:600;">{{ $letter->signed_name }}</td>
              </tr>
              <tr>
                <td style="padding:4px 0;font-size:13px;color:#888;">Date &amp; Time</td>
                <td style="padding:4px 0;font-size:13px;">{{ $letter->signed_at->format('d F Y \a\t H:i') }}</td>
              </tr>
              <tr>
                <td style="padding:4px 0;font-size:13px;color:#888;">IP Address</td>
                <td style="padding:4px 0;font-size:13px;font-family:monospace;">{{ $letter->signed_ip }}</td>
              </tr>
            </table>
          </td></tr>
        </table>

        <p style="margin:0;font-size:13px;color:#555;">Please keep this email and the attached PDF for your records. If you have any questions, please do not hesitate to contact us.</p>
      </td></tr>

      {{-- Footer --}}
      <tr><td style="padding:14px 32px;border-top:1px solid #e2e8f0;background:#fafafa;">
        <p style="margin:0;font-size:11px;color:#aaa;text-align:center;">Woods Accounting &amp; Consulting &nbsp;·&nbsp; {{ now()->format('d F Y') }}</p>
      </td></tr>

    </table>
  </td></tr>
</table>
</body>
</html>
