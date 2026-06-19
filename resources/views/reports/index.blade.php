@extends('layouts.app')

@section('title', 'Reports')
@section('page-title', 'Reports')

@section('content')
<div class="mb-4">
    <h4 class="mb-1">Reports</h4>
    <p class="text-muted small mb-0">Practice analytics and summaries</p>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <a href="{{ route('reports.fixed-prices') }}" class="text-decoration-none">
            <div class="card shadow-sm h-100 border-0" style="transition: transform .15s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform=''">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-3 bg-primary bg-opacity-10 p-3 me-3">
                            <i class="bi bi-currency-pound fs-4 text-primary"></i>
                        </div>
                        <div>
                            <h6 class="fw-semibold mb-0">Fixed Price Summary</h6>
                            <small class="text-muted">Total FPA & payroll FPA by client</small>
                        </div>
                    </div>
                    <p class="text-muted small mb-0">Aggregates all client FPA amounts and payroll FPA amounts with grand totals and interval breakdowns.</p>
                </div>
                <div class="card-footer bg-transparent border-top-0">
                    <small class="text-primary fw-semibold">View report <i class="bi bi-arrow-right ms-1"></i></small>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <a href="{{ route('reports.upcoming-jobs') }}" class="text-decoration-none">
            <div class="card shadow-sm h-100 border-0" style="transition: transform .15s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform=''">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-3 bg-warning bg-opacity-10 p-3 me-3">
                            <i class="bi bi-calendar-check fs-4 text-warning"></i>
                        </div>
                        <div>
                            <h6 class="fw-semibold mb-0">Upcoming Jobs</h6>
                            <small class="text-muted">Jobs due in the next 30 days</small>
                        </div>
                    </div>
                    <p class="text-muted small mb-0">All non-completed jobs due within 30 days, including overdue, broken down by team member.</p>
                </div>
                <div class="card-footer bg-transparent border-top-0">
                    <small class="text-warning fw-semibold">View report <i class="bi bi-arrow-right ms-1"></i></small>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <a href="{{ route('reports.custom.index') }}" class="text-decoration-none">
            <div class="card shadow-sm h-100 border-0" style="transition: transform .15s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform=''">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-3 bg-success bg-opacity-10 p-3 me-3">
                            <i class="bi bi-sliders fs-4 text-success"></i>
                        </div>
                        <div>
                            <h6 class="fw-semibold mb-0">Custom Reports</h6>
                            <small class="text-muted">Build and save your own reports</small>
                        </div>
                    </div>
                    <p class="text-muted small mb-0">Choose a data source, select columns, add filters, and save reports to run again at any time.</p>
                </div>
                <div class="card-footer bg-transparent border-top-0">
                    <small class="text-success fw-semibold">Open builder <i class="bi bi-arrow-right ms-1"></i></small>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection
