@extends('layouts.app')

@section('title', 'Activity Log')
@section('page-title', 'Activity Log')

@section('content')

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card h-100 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 flex-shrink-0" style="background:rgba(23,180,167,.12);">
                    <i class="bi bi-box-arrow-in-right fs-4" style="color:var(--brand-teal);"></i>
                </div>
                <div>
                    <div class="fs-3 fw-bold lh-1">{{ number_format($stats['logins_today']) }}</div>
                    <div class="text-muted small mt-1">Logins today</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card h-100 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 flex-shrink-0" style="background:rgba(26,157,217,.12);">
                    <i class="bi bi-person-check fs-4" style="color:var(--brand-blue);"></i>
                </div>
                <div>
                    <div class="fs-3 fw-bold lh-1">{{ number_format($stats['users_today']) }}</div>
                    <div class="text-muted small mt-1">Users active today</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card h-100 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 flex-shrink-0" style="background:rgba(247,148,29,.12);">
                    <i class="bi bi-collection fs-4" style="color:var(--brand-orange);"></i>
                </div>
                <div>
                    <div class="fs-3 fw-bold lh-1">{{ number_format($stats['active_sessions']) }}</div>
                    <div class="text-muted small mt-1">Sessions today</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card h-100 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 flex-shrink-0" style="background:rgba(12,61,56,.08);">
                    <i class="bi bi-activity fs-4" style="color:var(--brand-dark);"></i>
                </div>
                <div>
                    <div class="fs-3 fw-bold lh-1">{{ number_format($stats['total_today']) }}</div>
                    <div class="text-muted small mt-1">Events today</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('activity.index') }}" class="row g-2 align-items-end">
            <div class="col-sm-4 col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">User</label>
                <select name="user_id" class="form-select form-select-sm">
                    <option value="">All users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-4 col-md-2">
                <label class="form-label small fw-semibold text-muted mb-1">Event type</label>
                <select name="event" class="form-select form-select-sm">
                    <option value="">All events</option>
                    <option value="login"     @selected(request('event') === 'login')>Login</option>
                    <option value="logout"    @selected(request('event') === 'logout')>Logout</option>
                    <option value="page_view" @selected(request('event') === 'page_view')>Page view</option>
                </select>
            </div>
            <div class="col-sm-4 col-md-2">
                <label class="form-label small fw-semibold text-muted mb-1">From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control form-control-sm">
            </div>
            <div class="col-sm-4 col-md-2">
                <label class="form-label small fw-semibold text-muted mb-1">To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control form-control-sm">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-funnel me-1"></i>Filter
                </button>
                @if(request()->hasAny(['user_id','event','date_from','date_to']))
                    <a href="{{ route('activity.index') }}" class="btn btn-outline-secondary btn-sm ms-1">
                        <i class="bi bi-x-lg me-1"></i>Clear
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Log table --}}
<div class="card shadow-sm">
    <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between py-3">
        <h6 class="mb-0 fw-semibold">
            <i class="bi bi-clock-history me-2 text-muted"></i>Activity Log
        </h6>
        <span class="badge rounded-pill text-bg-secondary">{{ number_format($logs->total()) }} events</span>
    </div>

    @if($logs->isEmpty())
        <div class="card-body text-center text-muted py-5">
            <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
            No activity found for the selected filters.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width:160px;">When</th>
                        <th style="width:140px;">User</th>
                        <th style="width:90px;">Event</th>
                        <th>Description</th>
                        <th style="width:120px;">IP Address</th>
                        <th style="width:90px;" class="pe-3">Session</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                    <tr>
                        <td class="ps-3 text-muted small" title="{{ $log->created_at->format('d M Y H:i:s') }}">
                            {{ $log->created_at->diffForHumans() }}
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 fw-bold"
                                     style="width:28px;height:28px;font-size:.65rem;background:rgba(23,180,167,.15);color:var(--brand-teal);">
                                    {{ strtoupper(substr($log->user->name ?? '?', 0, 2)) }}
                                </div>
                                <span class="small fw-medium">{{ $log->user->name ?? '—' }}</span>
                            </div>
                        </td>
                        <td>
                            @if($log->event === 'login')
                                <span class="badge rounded-pill" style="background:rgba(23,180,167,.15);color:var(--brand-teal);">
                                    <i class="bi bi-box-arrow-in-right me-1"></i>Login
                                </span>
                            @elseif($log->event === 'logout')
                                <span class="badge rounded-pill" style="background:rgba(247,148,29,.15);color:var(--brand-orange);">
                                    <i class="bi bi-box-arrow-right me-1"></i>Logout
                                </span>
                            @else
                                <span class="badge rounded-pill bg-light text-muted border">
                                    <i class="bi bi-eye me-1"></i>View
                                </span>
                            @endif
                        </td>
                        <td class="small">{{ $log->description }}</td>
                        <td class="small text-muted font-monospace">{{ $log->ip_address ?? '—' }}</td>
                        <td class="pe-3 small text-muted font-monospace">
                            {{ $log->session_id ? substr($log->session_id, 0, 8) . '…' : '—' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-white border-top">
            @include('partials.pagination', ['paginator' => $logs])
        </div>
    @endif
</div>

@endsection
