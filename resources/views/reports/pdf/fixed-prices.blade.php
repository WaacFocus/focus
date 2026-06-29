<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    * { box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #222; margin: 0; padding: 20px; }
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

    /* Main table */
    h2 { font-size: 11px; text-transform: uppercase; letter-spacing: .04em; color: #666; margin: 0 0 6px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    thead th { background: #f0f1f3; padding: 5px 7px; text-align: left; font-size: 8px; text-transform: uppercase; letter-spacing: .03em; border-bottom: 2px solid #c8ccd0; }
    tbody td { padding: 5px 7px; border-bottom: 1px solid #e9ecef; font-size: 9.5px; }
    tbody tr:nth-child(even) td { background: #fafafa; }
    tfoot td { padding: 5px 7px; background: #f0f1f3; font-weight: bold; font-size: 9.5px; border-top: 2px solid #c8ccd0; }
    .text-right { text-align: right; }
    .badge { display: inline-block; padding: 1px 5px; background: #e9ecef; border-radius: 3px; font-size: 8px; }
    .generated { font-size: 8px; color: #aaa; margin-top: 12px; }
</style>
</head>
<body>

<h1>Billing</h1>
<div class="meta">Generated {{ now()->format('d F Y, H:i') }}</div>

<table class="summary-table">
    <tr>
        <td>
            <div class="lbl">Monthly Revenue</div>
            <div class="val" style="color:#17B4A7;">£{{ number_format($metrics['monthly'], 2) }}</div>
            <div class="sub">Per month (billed monthly)</div>
        </td>
        <td>
            <div class="lbl">Annual Revenue</div>
            <div class="val" style="color:#16a34a;">£{{ number_format($metrics['annual'], 2) }}</div>
            <div class="sub">Per year (billed annually)</div>
        </td>
        @if($metrics['quarterly'] > 0)
        <td>
            <div class="lbl">Quarterly Revenue</div>
            <div class="val" style="color:#F7941D;">£{{ number_format($metrics['quarterly'], 2) }}</div>
            <div class="sub">Per quarter (billed quarterly)</div>
        </td>
        @endif
        <td class="dark-cell">
            <div class="lbl">GRF — Gross Recurring Revenue</div>
            <div class="val">£{{ number_format($metrics['grf'], 2) }}</div>
            <div class="sub">Annualised (monthly ×12{{ $metrics['quarterly'] > 0 ? ' + quarterly ×4' : '' }} + annual)</div>
        </td>
    </tr>
</table>

<h2>Client Breakdown</h2>
<table>
    <thead>
        <tr>
            <th>Client</th>
            <th>Code</th>
            <th class="text-right">FPA Amount</th>
            <th>Interval</th>
        </tr>
    </thead>
    <tbody>
        @forelse($clients as $client)
        <tr>
            <td>{{ $client->company_name }}</td>
            <td>{{ $client->client_code ?: '—' }}</td>
            <td class="text-right">{{ $client->fpa_amount ? '£'.number_format($client->fpa_amount, 2) : '—' }}</td>
            <td>{{ $client->billing_interval ? ucfirst($client->billing_interval) : '—' }}</td>
        </tr>
        @empty
        <tr><td colspan="4" style="text-align:center;color:#888;">No clients with fixed prices.</td></tr>
        @endforelse
    </tbody>
    @if($clients->isNotEmpty())
    <tfoot>
        <tr>
            <td colspan="2">Totals</td>
            <td class="text-right">£{{ number_format($totalFpa, 2) }}</td>
            <td></td>
        </tr>
    </tfoot>
    @endif
</table>

<div class="generated">Focus — {{ now()->format('d/m/Y H:i') }}</div>
</body>
</html>
