@extends('layouts.app')

@section('title', 'Billing')
@section('page-title', 'Reports')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('reports.index') }}" class="text-muted text-decoration-none small no-print"><i class="bi bi-arrow-left me-1"></i>Reports</a>
        <h4 class="mb-0 mt-1">Billing</h4>
    </div>
    @include('reports._actions', [
        'csvUrl'         => route('reports.fixed-prices.csv'),
        'pdfPortraitUrl' => route('reports.fixed-prices.pdf', 'portrait'),
        'pdfLandscapeUrl'=> route('reports.fixed-prices.pdf', 'landscape'),
        'reportType'     => 'fixed-prices',
        'users'          => $users,
    ])
</div>

{{-- GRF summary --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="text-muted small mb-1 text-uppercase fw-semibold" style="font-size:.7rem;letter-spacing:.05em;">Monthly Fees</div>
                <div class="fs-4 fw-bold text-primary">£{{ number_format($metrics['monthly'], 2) }}</div>
                <div class="text-muted small">Per month billed monthly</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="text-muted small mb-1 text-uppercase fw-semibold" style="font-size:.7rem;letter-spacing:.05em;">Annual Fees</div>
                <div class="fs-4 fw-bold text-success">£{{ number_format($metrics['annual'], 2) }}</div>
                <div class="text-muted small">Per year billed annually</div>
            </div>
        </div>
    </div>
    @if($metrics['quarterly'] > 0)
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="text-muted small mb-1 text-uppercase fw-semibold" style="font-size:.7rem;letter-spacing:.05em;">Quarterly Fees</div>
                <div class="fs-4 fw-bold" style="color:var(--brand-orange,#F7941D);">£{{ number_format($metrics['quarterly'], 2) }}</div>
                <div class="text-muted small">Per quarter billed quarterly</div>
            </div>
        </div>
    </div>
    @endif
    <div class="col-md-{{ $metrics['quarterly'] > 0 ? 3 : 6 }}">
        <div class="card shadow-sm border-0" style="background:var(--brand-dark,#0C3D38);">
            <div class="card-body">
                <div class="text-white-50 small mb-1 text-uppercase fw-semibold" style="font-size:.7rem;letter-spacing:.05em;">GRF — Gross Recurring Fees</div>
                <div class="fs-4 fw-bold text-white">£{{ number_format($metrics['grf'], 2) }}</div>
                <div class="text-white-50 small">
                    Annualised (monthly ×12
                    @if($metrics['quarterly'] > 0) + quarterly ×4 @endif
                    + annual)
                </div>
            </div>
        </div>
    </div>
</div>

{{-- GRF breakdown bar --}}
@if($metrics['grf'] > 0)
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="small fw-semibold">GRF Composition</span>
            <span class="small text-muted">£{{ number_format($metrics['grf'], 2) }} / year</span>
        </div>
        <div class="progress" style="height:18px;border-radius:.375rem;">
            @php
                $monthlyPct   = $metrics['grf'] > 0 ? ($metrics['monthly'] * 12 / $metrics['grf']) * 100 : 0;
                $quarterlyPct = $metrics['grf'] > 0 ? ($metrics['quarterly'] * 4 / $metrics['grf']) * 100 : 0;
                $annualPct    = $metrics['grf'] > 0 ? ($metrics['annual'] / $metrics['grf']) * 100 : 0;
            @endphp
            @if($monthlyPct > 0)
            <div class="progress-bar" role="progressbar" style="width:{{ $monthlyPct }}%;background:#17B4A7;" title="Monthly ×12: £{{ number_format($metrics['monthly'] * 12, 2) }}">
                @if($monthlyPct > 8) Monthly @endif
            </div>
            @endif
            @if($quarterlyPct > 0)
            <div class="progress-bar" role="progressbar" style="width:{{ $quarterlyPct }}%;background:#F7941D;" title="Quarterly ×4: £{{ number_format($metrics['quarterly'] * 4, 2) }}">
                @if($quarterlyPct > 8) Qtrly @endif
            </div>
            @endif
            @if($annualPct > 0)
            <div class="progress-bar" role="progressbar" style="width:{{ $annualPct }}%;background:#0C3D38;" title="Annual: £{{ number_format($metrics['annual'], 2) }}">
                @if($annualPct > 8) Annual @endif
            </div>
            @endif
        </div>
        <div class="d-flex gap-3 mt-2 flex-wrap">
            @if($monthlyPct > 0)<span class="small"><span class="d-inline-block rounded me-1" style="width:10px;height:10px;background:#17B4A7;"></span>Monthly ×12 = £{{ number_format($metrics['monthly'] * 12, 2) }}</span>@endif
            @if($quarterlyPct > 0)<span class="small"><span class="d-inline-block rounded me-1" style="width:10px;height:10px;background:#F7941D;"></span>Quarterly ×4 = £{{ number_format($metrics['quarterly'] * 4, 2) }}</span>@endif
            @if($annualPct > 0)<span class="small"><span class="d-inline-block rounded me-1" style="width:10px;height:10px;background:#0C3D38;"></span>Annual = £{{ number_format($metrics['annual'], 2) }}</span>@endif
        </div>
    </div>
</div>
@endif

{{-- Client breakdown --}}
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center" style="background:var(--brand-dark,#0C3D38);">
        <span class="fw-semibold text-white">Monthly Fees Breakdown</span>
        <span class="small fw-normal" style="color:rgba(255,255,255,.6);">{{ $clients->count() }} clients with fixed prices</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <colgroup>
                <col style="width:40%">
                <col style="width:20%">
                <col style="width:40%">
            </colgroup>
            <thead class="table-light">
                <tr>
                    <th>Client</th>
                    <th>Code</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $client)
                <tr>
                    <td><a href="{{ route('clients.show', $client) }}" class="fw-medium text-decoration-none">{{ $client->company_name }}</a></td>
                    <td><span class="text-muted small">{{ $client->client_code ?: '—' }}</span></td>
                    <td class="text-end">{{ $client->fpa_amount ? '£'.number_format($client->fpa_amount, 2) : '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center text-muted py-4">No clients have fixed price amounts set.</td></tr>
                @endforelse
            </tbody>
            @if($clients->isNotEmpty())
            <tfoot class="table-light">
                <tr class="fw-semibold">
                    <td colspan="2">Totals</td>
                    <td class="text-end">£{{ number_format($totalFpa, 2) }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

{{-- Annual fees breakdown --}}
@php
    $annualFpaClients = $clients->where('billing_interval', 'annually')->values();
    $annualTotal = $annualFpaClients->sum('fpa_amount') + $annualLines->sum('amount');
@endphp
@if($annualTotal > 0)
<div class="card shadow-sm mt-4">
    <div class="card-header d-flex justify-content-between align-items-center" style="background:var(--brand-dark,#0C3D38);">
        <span class="fw-semibold text-white">Annual Fees Breakdown</span>
        <span class="small fw-normal" style="color:rgba(255,255,255,.6);">£{{ number_format($annualTotal, 2) }} / year</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <colgroup>
                <col style="width:40%">
                <col style="width:20%">
                <col style="width:15%">
                <col style="width:25%">
            </colgroup>
            <thead class="table-light">
                <tr>
                    <th>Client</th>
                    <th>Code</th>
                    <th>Description</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($annualFpaClients as $c)
                <tr>
                    <td>
                        <a href="{{ route('clients.show', $c) }}" class="fw-medium text-decoration-none">{{ $c->company_name }}</a>
                    </td>
                    <td><span class="text-muted small">{{ $c->client_code ?: '—' }}</span></td>
                    <td class="text-muted small">Fixed Price Agreement</td>
                    <td class="text-end">£{{ number_format($c->fpa_amount, 2) }}</td>
                </tr>
                @endforeach
                @foreach($annualLines as $line)
                <tr>
                    <td>
                        <a href="{{ route('clients.show', $line->client) }}" class="fw-medium text-decoration-none">{{ $line->client->company_name }}</a>
                    </td>
                    <td><span class="text-muted small">{{ $line->client->client_code ?: '—' }}</span></td>
                    <td class="text-muted small">{{ $line->description ?: '—' }}</td>
                    <td class="text-end">£{{ number_format($line->amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="table-light">
                <tr class="fw-semibold">
                    <td colspan="3">Total</td>
                    <td class="text-end">£{{ number_format($annualTotal, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endif
@endsection
