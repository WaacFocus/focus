<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
* { box-sizing: border-box; }
body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #222; margin: 0; padding: 24px 32px; }

.header { border-bottom: 3px solid #0C3D38; padding-bottom: 12px; margin-bottom: 20px; }
.header-top { display: flex; justify-content: space-between; align-items: flex-start; }
.firm-name { font-size: 16px; font-weight: bold; color: #0C3D38; }
.letter-meta { font-size: 9px; color: #888; text-align: right; }

.client-block { margin-bottom: 20px; }
.client-block .label { font-size: 8px; text-transform: uppercase; color: #999; margin-bottom: 2px; letter-spacing: .04em; }
.client-block .value { font-size: 11px; font-weight: bold; }

.salutation { font-size: 11px; margin-bottom: 16px; }

.section { margin-bottom: 14px; }
.section h3 { font-size: 10.5px; color: #0C3D38; font-weight: bold; margin: 0 0 4px; text-transform: uppercase; letter-spacing: .03em; }
.section p { font-size: 10px; margin: 0; line-height: 1.6; white-space: pre-line; }

.signature-block { margin-top: 32px; border-top: 2px solid #0C3D38; padding-top: 14px; }
.sig-title { font-size: 9px; text-transform: uppercase; color: #999; letter-spacing: .04em; margin-bottom: 8px; }
.sig-row { display: flex; margin-bottom: 4px; }
.sig-label { font-size: 9px; color: #888; width: 100px; flex-shrink: 0; }
.sig-value { font-size: 9px; font-weight: bold; }
.sig-ip { font-size: 8.5px; font-family: monospace; }

.footer { margin-top: 24px; border-top: 1px solid #e9ecef; padding-top: 8px; font-size: 8px; color: #aaa; display: flex; justify-content: space-between; }
</style>
</head>
<body>

<div class="header">
    <div class="header-top">
        <div class="firm-name">{{ config('services.smtp2go.from_name', 'Focus Accounting') }}</div>
        <div class="letter-meta">
            <div>ENGAGEMENT LETTER</div>
            <div>{{ now()->format('d F Y') }}</div>
        </div>
    </div>
</div>

<div class="client-block">
    <div class="label">Prepared for</div>
    <div class="value">{{ $letter->client->company_name }}</div>
    @if($letter->client->contact_name)
    <div style="font-size:10px;color:#555;">{{ $letter->client->contact_name }}</div>
    @endif
</div>

<p class="salutation">Dear {{ $letter->client->contact_name ?: $letter->client->company_name }},</p>

@foreach($letter->sections as $section)
<div class="section">
    <h3>{{ $section['title'] }}</h3>
    <p>{{ $section['body'] }}</p>
</div>
@endforeach

<div class="signature-block">
    <div class="sig-title">Digital Signature Record</div>
    <div class="sig-row"><span class="sig-label">Signed by</span><span class="sig-value">{{ $letter->signed_name }}</span></div>
    <div class="sig-row"><span class="sig-label">Date &amp; Time</span><span class="sig-value">{{ $letter->signed_at->format('d F Y \a\t H:i:s') }} UTC</span></div>
    <div class="sig-row"><span class="sig-label">IP Address</span><span class="sig-value sig-ip">{{ $letter->signed_ip }}</span></div>
    <div class="sig-row"><span class="sig-label">Status</span><span class="sig-value" style="color:#16a34a;">Signed &amp; Accepted</span></div>
</div>

<div class="footer">
    <span>Focus &mdash; Engagement Letter</span>
    <span>Generated {{ now()->format('d/m/Y H:i') }}</span>
</div>

</body>
</html>
