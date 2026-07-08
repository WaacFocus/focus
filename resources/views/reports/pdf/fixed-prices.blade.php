<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    * { box-sizing: border-box; }
    body { font-family: Arial, sans-serif; font-size: 10px; color: #222; margin: 0; padding: 20px; }
    h1  { font-size: 16px; margin: 0 0 2px; }
    .meta { font-size: 9px; color: #888; margin-bottom: 16px; }

    /* Summary row */
    .summary-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
    .summary-table td { border: 1px solid #dee2e6; padding: 8px 10px; width: 33.3%; vertical-align: top; }
    .summary-table .lbl { font-size: 8px; text-transform: uppercase; color: #666; margin-bottom: 3px; }
    .summary-table .val { font-size: 14px; font-weight: bold; }
    .summary-table .sub { font-size: 8px; color: #888; margin-top: 2px; }
    .dark-cell { background: #0C3D38; color: #fff; }
    .dark-cell .lbl { color: rgba(255,255,255,.6); }
    .dark-cell .sub { color: rgba(255,255,255,.5); }

    /* Section header */
    .section-header { background: #0C3D38; color: #fff; padding: 6px 8px; font-size: 10px; font-weight: bold; margin-bottom: 0; display: flex; justify-content: space-between; }
    .section-header span { color: rgba(255,255,255,.6); font-weight: normal; font-size: 9px; }

    /* Main table */
    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    thead th { background: #f0f1f3; padding: 5px 7px; text-align: left; font-size: 8px; text-transform: uppercase; letter-spacing: .03em; border-bottom: 2px solid #c8ccd0; }
    thead th.text-right { text-align: right; }
    tbody td { padding: 5px 7px; border-bottom: 1px solid #e9ecef; font-size: 9.5px; }
    tbody tr:nth-child(even) td { background: #fafafa; }
    tfoot td { padding: 5px 7px; background: #f0f1f3; font-weight: bold; font-size: 9.5px; border-top: 2px solid #c8ccd0; }
    .text-right { text-align: right; }
    .generated { font-size: 8px; color: #aaa; margin-top: 12px; }
</style>
</head>
<body>

@php
    $logoData = base64_encode(file_get_contents(public_path('images/woods-logo.png')));
    $logoSrc  = 'data:image/png;base64,' . $logoData;
@endphp

<table style="width:100%;border-collapse:collapse;margin-bottom:16px;">
    <tr>
        <td><img src="{{ $logoSrc }}" style="height:38px;" alt="Logo"></td>
        <td style="text-align:right;vertical-align:bottom;">
            <span style="font-size:16px;font-weight:bold;color:#0C3D38;">Billing</span><br>
            <span style="font-size:9px;color:#888;">Generated {{ now()->format('d F Y, H:i') }}</span>
        </td>
    </tr>
</table>
<hr style="border:none;border-top:2px solid #0C3D38;margin-bottom:16px;">

<table class="summary-table">
    <tr>
        <td>
            <div class="lbl">Monthly Fees</div>
            <div class="val" style="color:#17B4A7;">£{{ number_format($metrics['monthly'], 2) }}</div>
            <div class="sub">Per month (billed monthly)</div>
        </td>
        <td>
            <div class="lbl">Annual Fees</div>
            <div class="val" style="color:#16a34a;">£{{ number_format($metrics['annual'], 2) }}</div>
            <div class="sub">Per year (billed annually)</div>
        </td>
        @if($metrics['quarterly'] > 0)
        <td>
            <div class="lbl">Quarterly Fees</div>
            <div class="val" style="color:#F7941D;">£{{ number_format($metrics['quarterly'], 2) }}</div>
            <div class="sub">Per quarter (billed quarterly)</div>
        </td>
        @endif
        <td class="dark-cell">
            <div class="lbl">GRF — Gross Recurring Fees</div>
            <div class="val">£{{ number_format($metrics['grf'], 2) }}</div>
            <div class="sub">Annualised (monthly ×12{{ $metrics['quarterly'] > 0 ? ' + quarterly ×4' : '' }} + annual)</div>
        </td>
    </tr>
</table>

{{-- Monthly Fees Breakdown --}}
<div class="section-header">
    Monthly Fees Breakdown
    <span>{{ $clients->count() }} clients</span>
</div>
<table>
    <thead>
        <tr>
            <th style="width:40%;">Client</th>
            <th style="width:20%;">Code</th>
            <th class="text-right" style="width:40%;">Amount</th>
        </tr>
    </thead>
    <tbody>
        @forelse($clients as $client)
        <tr>
            <td>{{ $client->company_name }}</td>
            <td>{{ $client->client_code ?: '—' }}</td>
            <td class="text-right">{{ $client->fpa_amount ? '£'.number_format($client->fpa_amount, 2) : '—' }}</td>
        </tr>
        @empty
        <tr><td colspan="3" style="text-align:center;color:#888;">No clients with fixed prices.</td></tr>
        @endforelse
    </tbody>
    @if($clients->isNotEmpty())
    <tfoot>
        <tr>
            <td colspan="2">Totals</td>
            <td class="text-right">£{{ number_format($totalFpa, 2) }}</td>
        </tr>
    </tfoot>
    @endif
</table>

{{-- Annual Fees Breakdown --}}
@if($annualTotal > 0)
<div class="section-header">
    Annual Fees Breakdown
    <span>£{{ number_format($annualTotal, 2) }} / year</span>
</div>
<table>
    <thead>
        <tr>
            <th style="width:40%;">Client</th>
            <th style="width:20%;">Code</th>
            <th style="width:15%;">Description</th>
            <th class="text-right" style="width:25%;">Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($annualFpaClients as $c)
        <tr>
            <td>{{ $c->company_name }}</td>
            <td>{{ $c->client_code ?: '—' }}</td>
            <td style="color:#888;">Fixed Price Agreement</td>
            <td class="text-right">£{{ number_format($c->fpa_amount, 2) }}</td>
        </tr>
        @endforeach
        @foreach($annualLines as $line)
        <tr>
            <td>{{ $line->client->company_name }}</td>
            <td>{{ $line->client->client_code ?: '—' }}</td>
            <td style="color:#888;">{{ $line->description ?: '—' }}</td>
            <td class="text-right">£{{ number_format($line->amount, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3">Total</td>
            <td class="text-right">£{{ number_format($annualTotal, 2) }}</td>
        </tr>
    </tfoot>
</table>
@endif

<div class="generated">Focus — {{ now()->format('d/m/Y H:i') }}</div>
</body>
</html>
