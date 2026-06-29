<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
* { box-sizing: border-box; }
body { font-family: Arial, sans-serif; font-size: 10px; color: #222; margin: 0; padding: 24px 32px; }

.header { border-bottom: 3px solid #3DBFB8; padding-bottom: 14px; margin-bottom: 20px; }
.header-top { display: flex; justify-content: space-between; align-items: flex-start; }
.logo { height: 36px; }
.letter-meta { font-size: 9px; color: #888; text-align: right; }
.letter-meta .label { font-size: 8px; text-transform: uppercase; letter-spacing: .06em; color: #3DBFB8; font-weight: bold; }

.client-block { margin-bottom: 20px; }
.client-block .label { font-size: 8px; text-transform: uppercase; color: #3DBFB8; margin-bottom: 2px; letter-spacing: .06em; font-weight: bold; }
.client-block .value { font-size: 11px; font-weight: bold; }

.salutation { font-size: 11px; margin-bottom: 16px; }

.section { margin-bottom: 14px; }
.section h3 { font-size: 10px; color: #3DBFB8; font-weight: bold; margin: 0 0 4px; text-transform: uppercase; letter-spacing: .05em; }
.section p { font-size: 10px; margin: 0; line-height: 1.65; white-space: pre-line; }

.signature-block { margin-top: 32px; border-top: 2px solid #3DBFB8; padding-top: 14px; }
.sig-title { font-size: 8px; text-transform: uppercase; color: #3DBFB8; letter-spacing: .06em; font-weight: bold; margin-bottom: 8px; }
.sig-row { display: flex; margin-bottom: 4px; }
.sig-label { font-size: 9px; color: #888; width: 100px; flex-shrink: 0; }
.sig-value { font-size: 9px; font-weight: bold; }
.sig-ip { font-size: 8.5px; font-family: monospace; }
.sig-status { color: #3DBFB8; }

.footer { margin-top: 24px; border-top: 1px solid #e9ecef; padding-top: 8px; font-size: 8px; color: #aaa; display: flex; justify-content: space-between; }
</style>
</head>
<body>

@php
    $logoData = base64_encode(file_get_contents(public_path('images/woods-logo.png')));
    $logoSrc  = 'data:image/png;base64,' . $logoData;
@endphp

<div class="header">
    <div class="header-top">
        <img src="{{ $logoSrc }}" class="logo" alt="Woods">
        <div class="letter-meta">
            <div class="label">Engagement Letter</div>
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

<p class="salutation">Dear {{ $letter->client->contact_formal }},</p>

@foreach($letter->sections as $section)
<div class="section">
    <h3>{{ $section['title'] }}</h3>
    <p>{{ $section['body'] }}</p>
</div>
@endforeach

@if($letter->signed_at)
<div class="signature-block">
    <div class="sig-title">Digital Signature Record</div>
    <div class="sig-row"><span class="sig-label">Signed by</span><span class="sig-value">{{ $letter->signed_name }}</span></div>
    <div class="sig-row"><span class="sig-label">Date &amp; Time</span><span class="sig-value">{{ $letter->signed_at->format('d F Y \a\t H:i:s') }} UTC</span></div>
    <div class="sig-row"><span class="sig-label">IP Address</span><span class="sig-value sig-ip">{{ $letter->signed_ip }}</span></div>
    <div class="sig-row"><span class="sig-label">Status</span><span class="sig-value sig-status">Signed &amp; Accepted</span></div>
</div>
@endif

<div class="footer">
    <span>Woods Accounting &amp; Consulting &mdash; Engagement Letter</span>
    <span>Generated {{ now()->format('d/m/Y H:i') }}</span>
</div>

</body>
</html>
