@extends('layouts.app')

@section('title', $client->company_name)
@section('page-title', $client->company_name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        @if($client->client_code)
            <div class="text-muted small fw-semibold mb-1" style="letter-spacing:.04em;">{{ $client->client_code }}</div>
        @endif
        <h4 class="mb-1">{{ $client->company_name }}</h4>
        <span class="badge bg-{{ $client->status_badge }}">{{ ucfirst($client->status) }}</span>
        @if($client->client_type_id)
            <span class="badge bg-light text-dark border ms-1">{{ $client->clientType?->name }}</span>
        @endif
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('renewals.create', ['client_id' => $client->id]) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-repeat me-1"></i>Add Renewal</a>
        <button type="button" class="btn btn-primary" onclick="openClientPanel({{ $client->id }})"><i class="bi bi-pencil me-1"></i>Edit</button>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold">Contact Details</div>
            <div class="card-body">
                @if($client->contact_name)
                    <div class="mb-2"><i class="bi bi-person me-2 text-muted"></i>{{ $client->contact_name }}</div>
                @endif
                @if($client->email)
                    <div class="mb-2"><i class="bi bi-envelope me-2 text-muted"></i><a href="mailto:{{ $client->email }}">{{ $client->email }}</a></div>
                @endif
                @if($client->phone)
                    <div class="mb-2"><i class="bi bi-telephone me-2 text-muted"></i><a href="tel:{{ $client->phone }}">{{ $client->phone }}</a></div>
                @endif
                @if($client->address || $client->town)
                    <div class="mb-2"><i class="bi bi-geo-alt me-2 text-muted"></i>
                        {{ implode(', ', array_filter([$client->address, $client->town, $client->county, $client->postcode])) }}
                    </div>
                @endif
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold">Tax & Regulatory</div>
            <div class="card-body">
                @if($client->vat_number)
                    <div class="mb-2"><small class="text-muted">VAT:</small> {{ $client->vat_number }}</div>
                @endif
                @if($client->company_number)
                    <div class="mb-2"><small class="text-muted">Company No:</small> {{ $client->company_number }}</div>
                @endif
                @if($client->utr_number)
                    <div class="mb-2"><small class="text-muted">UTR:</small> {{ $client->utr_number }}</div>
                @endif
                @if($client->paye_ref)
                    <div class="mb-2"><small class="text-muted">PAYE Ref:</small> {{ $client->paye_ref }}</div>
                @endif
                @if(!$client->vat_number && !$client->company_number && !$client->utr_number && !$client->paye_ref)
                    <div class="text-muted small">No tax details recorded.</div>
                @endif
            </div>
        </div>

        @if($client->ch_status || $client->ch_incorporated_on || $client->ch_sic_codes || $client->ch_accounts_year_end)
        @php
            $jurisdictionMap = [
                'england-wales'          => 'England & Wales',
                'scotland'               => 'Scotland',
                'northern-ireland'       => 'Northern Ireland',
                'england'                => 'England',
                'wales'                  => 'Wales',
                'united-kingdom'         => 'United Kingdom',
                'european-union'         => 'European Union',
            ];
        @endphp
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-building me-2 text-muted"></i>Companies House
            </div>
            <div class="card-body">
                @if($client->ch_status)
                <div class="mb-2">
                    <small class="text-muted">Status:</small>
                    <span class="badge {{ $client->ch_status === 'active' ? 'bg-success' : 'bg-secondary' }} ms-1">
                        {{ ucfirst(str_replace('-', ' ', $client->ch_status)) }}
                    </span>
                </div>
                @endif
                @if($client->ch_incorporated_on)
                <div class="mb-2"><small class="text-muted">Incorporated:</small> {{ $client->ch_incorporated_on->format('d M Y') }}</div>
                @endif
                @if($client->ch_jurisdiction)
                <div class="mb-2"><small class="text-muted">Jurisdiction:</small>
                    {{ $jurisdictionMap[$client->ch_jurisdiction] ?? ucwords(str_replace('-', ' ', $client->ch_jurisdiction)) }}
                </div>
                @endif
                @if($client->ch_sic_codes)
                <div class="mb-2"><small class="text-muted">SIC Codes:</small> {{ $client->ch_sic_codes }}</div>
                @endif
                @if($client->ch_accounts_year_end || $client->ch_accounts_next_due)
                <hr class="my-2">
                @endif
                @if($client->ch_accounts_year_end)
                <div class="mb-2">
                    <small class="text-muted">Accounts Year End:</small> {{ $client->ch_accounts_year_end->format('d M Y') }}
                </div>
                @endif
                @if($client->ch_accounts_next_due)
                <div class="mb-2">
                    <small class="text-muted">Accounts Due:</small>
                    <span class="{{ $client->ch_accounts_next_due->isPast() ? 'text-danger fw-semibold' : '' }}">
                        {{ $client->ch_accounts_next_due->format('d M Y') }}
                    </span>
                    @if($client->ch_accounts_next_due->isPast())
                        <span class="badge bg-danger ms-1">Overdue</span>
                    @elseif($client->ch_accounts_next_due->diffInDays(now()) > -30)
                        <span class="badge bg-warning text-dark ms-1">Due soon</span>
                    @endif
                </div>
                @endif
                @if($client->ch_confirmation_statement_next_due)
                <div class="mb-2">
                    <small class="text-muted">Conf. Statement Due:</small>
                    <span class="{{ $client->ch_confirmation_statement_next_due->isPast() ? 'text-danger fw-semibold' : '' }}">
                        {{ $client->ch_confirmation_statement_next_due->format('d M Y') }}
                    </span>
                    @if($client->ch_confirmation_statement_next_due->isPast())
                        <span class="badge bg-danger ms-1">Overdue</span>
                    @elseif($client->ch_confirmation_statement_next_due->diffInDays(now()) > -30)
                        <span class="badge bg-warning text-dark ms-1">Due soon</span>
                    @endif
                </div>
                @endif
            </div>
        </div>
        @endif

        @if($client->fpa_amount || $client->billingLines->isNotEmpty())
        @php
            $annualFee = 0;
            $toAnnual  = fn($amt, $interval) => match($interval) {
                'monthly'  => $amt * 12,
                'quarterly'=> $amt * 4,
                default    => $amt,
            };
            if ($client->fpa_amount) $annualFee += $toAnnual((float)$client->fpa_amount, $client->billing_interval ?? 'annually');
            foreach ($client->billingLines as $bl) {
                $annualFee += $toAnnual((float)$bl->amount, $bl->interval);
            }
        @endphp
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold">Fixed Price Agreement</div>
            <div class="card-body">
                @if($client->fpa_amount)
                <div class="mb-2">
                    <small class="text-muted">FPA:</small>
                    <strong>£{{ number_format($client->fpa_amount, 2) }}</strong>
                    @if($client->billing_interval)
                        <span class="badge bg-light text-dark ms-1">{{ ucfirst($client->billing_interval) }}</span>
                    @endif
                </div>
                @endif
                @if($client->fpa_year_end)
                <div class="mb-2"><small class="text-muted">Year End:</small> {{ $client->fpa_year_end->format('d M Y') }}</div>
                @endif
                @if($client->payment_method)
                <div class="mb-2"><small class="text-muted">Payment:</small> {{ $client->payment_method }}</div>
                @endif

                @foreach($client->billingLines as $line)
                <div class="mb-2">
                    <small class="text-muted">{{ $line->description ?: 'Additional line' }}:</small>
                    <strong>£{{ number_format($line->amount, 2) }}</strong>
                    <span class="badge bg-light text-dark ms-1">{{ ucfirst($line->interval) }}</span>
                </div>
                @endforeach

                <hr class="my-2">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted fw-semibold text-uppercase" style="letter-spacing:.04em;">Annual Total Fee</small>
                    <span class="fw-bold fs-6">£{{ number_format($annualFee, 2) }}</span>
                </div>
            </div>
        </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Services ({{ $client->services->count() }})</span>
                @if($availableServices->isNotEmpty())
                <button type="button" class="btn btn-sm btn-outline-primary"
                        data-bs-toggle="offcanvas" data-bs-target="#servicePanel">
                    <i class="bi bi-plus-lg me-1"></i>Add
                </button>
                @endif
            </div>
            <div class="list-group list-group-flush">
                @forelse($client->services as $service)
                <div class="list-group-item d-flex justify-content-between align-items-start gap-2">
                    <div class="flex-grow-1">
                        <div class="fw-medium">{{ $service->name }}</div>
                        <small class="text-muted">
                            £{{ number_format($service->pivot->price_override ?? $service->default_price, 2) }}
                            / {{ $service->billing_cycle_label }}
                            @if($service->pivot->start_date)
                                &middot; From {{ \Carbon\Carbon::parse($service->pivot->start_date)->format('d M Y') }}
                            @endif
                            @if($service->pivot->end_date)
                                &middot; To {{ \Carbon\Carbon::parse($service->pivot->end_date)->format('d M Y') }}
                            @endif
                        </small>
                        @if($service->pivot->notes)
                            <div class="text-muted small fst-italic mt-1">{{ $service->pivot->notes }}</div>
                        @endif
                    </div>
                    @can('manager')
                    <form method="POST"
                          action="{{ route('clients.services.destroy', [$client, $service]) }}"
                          onsubmit="return confirm('Remove {{ addslashes($service->name) }} from this client?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger flex-shrink-0" title="Remove service">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                    @endcan
                </div>
                @empty
                <div class="list-group-item text-muted small">No services assigned yet.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        @if($client->notes)
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold">Notes</div>
            <div class="card-body">{{ $client->notes }}</div>
        </div>
        @endif

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold"><i class="bi bi-briefcase me-2 text-primary"></i>Jobs ({{ $client->jobs->whereNotIn('status', ['completed'])->count() }} active)</span>
                <a href="{{ route('jobs.index', ['client_id' => $client->id]) }}" class="btn btn-sm btn-outline-secondary">View all</a>
            </div>
            @php $activeJobs = $client->jobs->whereNotIn('status', ['completed'])->sortBy('due_date'); @endphp
            @if($activeJobs->count())
            <div class="table-responsive">
                <table class="table mb-0 align-middle small">
                    <thead class="table-light">
                        <tr><th>Job</th><th>Assigned To</th><th>Freq</th><th>Due</th><th class="text-center">Status</th></tr>
                    </thead>
                    <tbody>
                        @foreach($activeJobs as $job)
                        <tr class="{{ $job->due_date->isPast() ? 'table-danger' : '' }}">
                            <td class="fw-semibold">{{ $job->name }}</td>
                            <td>{{ $job->assignedTo->name }}</td>
                            <td><span class="badge bg-light text-dark">{{ $job->frequency_label }}</span></td>
                            <td class="{{ $job->due_date->isPast() ? 'text-danger fw-semibold' : '' }}">
                                {{ $job->due_date->format('d M Y') }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $job->status_badge }}">{{ ucfirst(str_replace('_', ' ', $job->status)) }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="card-body text-muted small">No active jobs for this client.</div>
            @endif
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Engagement Letters ({{ $client->renewals->count() }})</span>
                <a href="{{ route('renewals.create', ['client_id' => $client->id]) }}" class="btn btn-sm btn-outline-primary">Add Letter</a>
            </div>
            @if($client->renewals->isNotEmpty())
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead class="table-light">
                        <tr><th>Letter Type</th><th>Last Completed</th><th>Next Due</th><th>Status</th><th></th></tr>
                    </thead>
                    <tbody>
                        @foreach($client->renewals as $renewal)
                        <tr>
                            <td>{{ $renewal->description }}</td>
                            <td class="text-muted small">{{ $renewal->completed_date ? $renewal->completed_date->format('d M Y') : '—' }}</td>
                            <td class="{{ $renewal->is_overdue ? 'text-danger fw-semibold' : '' }}">
                                {{ $renewal->due_date ? $renewal->due_date->format('d M Y') : '—' }}
                                @if($renewal->is_overdue)<span class="badge bg-danger ms-1">Overdue</span>@endif
                            </td>
                            <td><span class="badge bg-{{ $renewal->status_badge }}">{{ ucfirst($renewal->status) }}</span></td>
                            <td><a href="{{ route('renewals.edit', $renewal) }}" class="btn btn-sm btn-outline-secondary">Edit</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="card-body text-muted small">No engagement letters yet.</div>
            @endif
        </div>
    </div>
</div>
@include('clients._panel')
@include('clients._service_panel')
@endsection
