<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Engagement Letter</title>
</head>
<body style="margin:0;padding:0;background:#f4f7f6;font-family:Arial,Helvetica,sans-serif;color:#1a1a1a;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f7f6;padding:24px 0;">
  <tr><td align="center">
    <table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.1);">

      <tr><td style="background:#0C3D38;padding:28px 32px;">
        <p style="margin:0;font-size:11px;color:rgba(255,255,255,.55);letter-spacing:.06em;text-transform:uppercase;">Engagement Letter</p>
        <h1 style="margin:6px 0 4px;font-size:22px;color:#fff;font-weight:700;">Please Review &amp; Sign</h1>
        <p style="margin:0;font-size:13px;color:rgba(255,255,255,.65);">{{ $client->company_name }}</p>
      </td></tr>

      <tr><td style="padding:28px 32px;">
        <p style="margin:0 0 16px;">Dear {{ $client->contact_name ?: $client->company_name }},</p>
        <p style="margin:0 0 16px;">We have prepared your engagement letter for your review. Please read it carefully and sign it using the button below.</p>
        <p style="margin:0 0 28px;">Your IP address and the time of signing will be recorded for our records. Once signed, you will receive a copy by email.</p>

        <table width="100%" cellpadding="0" cellspacing="0">
          <tr><td align="center">
            <a href="{{ $signingUrl }}"
               style="display:inline-block;background:#0C3D38;color:#fff;text-decoration:none;padding:14px 36px;border-radius:6px;font-size:16px;font-weight:600;">
              Review &amp; Sign Engagement Letter
            </a>
          </td></tr>
        </table>

        <p style="margin:28px 0 0;font-size:12px;color:#888;text-align:center;">
          Or copy this link: <a href="{{ $signingUrl }}" style="color:#17B4A7;word-break:break-all;">{{ $signingUrl }}</a>
        </p>
      </td></tr>

      <tr><td style="padding:16px 32px;border-top:1px solid #e2e8f0;">
        <p style="margin:0;font-size:11px;color:#aaa;text-align:center;">
          This link is unique to you. Please do not share it. &nbsp;·&nbsp; {{ now()->format('d F Y') }}
        </p>
      </td></tr>

    </table>
  </td></tr>
</table>
</body>
</html>
