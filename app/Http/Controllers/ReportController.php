<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Job;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    private function upcomingJobsData(): \Illuminate\Database\Eloquent\Collection
    {
        return Job::with(['client', 'assignedTo'])
            ->whereNotIn('status', ['completed'])
            ->where('due_date', '<=', now()->addDays(30))
            ->orderBy('due_date')
            ->get();
    }

    public function upcomingJobs()
    {
        $jobs          = $this->upcomingJobsData();
        $overdueCount  = $jobs->filter(fn($j) => $j->due_date->isPast() && !$j->due_date->isToday())->count();
        $todayCount    = $jobs->filter(fn($j) => $j->due_date->isToday())->count();
        $upcomingCount = $jobs->filter(fn($j) => $j->due_date->isFuture())->count();
        $byUser        = $jobs->groupBy(fn($j) => $j->assignedTo->name);

        return view('reports.upcoming-jobs', compact('jobs', 'overdueCount', 'todayCount', 'upcomingCount', 'byUser'));
    }

    public function upcomingJobsPdf(string $orientation = 'landscape')
    {
        $jobs         = $this->upcomingJobsData();
        $overdueCount = $jobs->filter(fn($j) => $j->due_date->isPast() && !$j->due_date->isToday())->count();

        $pdf = Pdf::loadView('reports.pdf.upcoming-jobs', compact('jobs', 'overdueCount'))
            ->setPaper('A4', $orientation);

        return $pdf->download('upcoming-jobs-' . now()->format('Y-m-d') . '-' . $orientation . '.pdf');
    }

    public function upcomingJobsCsv(): StreamedResponse
    {
        $jobs     = $this->upcomingJobsData();
        $filename = 'upcoming-jobs-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($jobs) {
            $out = fopen('php://output', 'w');

            fputcsv($out, ['Job', 'Client', 'Assigned To', 'Frequency', 'Due Date', 'Status']);

            foreach ($jobs as $j) {
                fputcsv($out, [
                    $j->name,
                    $j->client?->company_name ?? '',
                    $j->assignedTo->name,
                    $j->frequency_label,
                    $j->due_date->format('d/m/Y'),
                    ucfirst(str_replace('_', ' ', $j->status)),
                ]);
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function fixedPrices()
    {
        $clients = Client::whereNotNull('fpa_amount')
            ->orWhereNotNull('payroll_fpa')
            ->orderBy('company_name')
            ->get(['id', 'company_name', 'client_code', 'status', 'fpa_amount', 'billing_interval', 'payroll_fpa', 'payroll_billing_interval']);

        $totalFpa          = $clients->sum('fpa_amount');
        $totalPayrollFpa   = $clients->sum('payroll_fpa');
        $grandTotal        = $totalFpa + $totalPayrollFpa;

        $byInterval = $clients->groupBy(fn ($c) => $c->billing_interval ?: 'Unspecified');

        return view('reports.fixed-prices', compact('clients', 'totalFpa', 'totalPayrollFpa', 'grandTotal', 'byInterval'));
    }

    public function fixedPricesPdf(string $orientation = 'portrait')
    {
        $clients = Client::whereNotNull('fpa_amount')
            ->orWhereNotNull('payroll_fpa')
            ->orderBy('company_name')
            ->get(['id', 'company_name', 'client_code', 'status', 'fpa_amount', 'billing_interval', 'payroll_fpa', 'payroll_billing_interval']);

        $totalFpa        = $clients->sum('fpa_amount');
        $totalPayrollFpa = $clients->sum('payroll_fpa');
        $grandTotal      = $totalFpa + $totalPayrollFpa;

        $pdf = Pdf::loadView('reports.pdf.fixed-prices', compact('clients', 'totalFpa', 'totalPayrollFpa', 'grandTotal'))
            ->setPaper('A4', $orientation);

        return $pdf->download('fixed-prices-' . now()->format('Y-m-d') . '-' . $orientation . '.pdf');
    }

    public function fixedPricesCsv(): StreamedResponse
    {
        $clients = Client::whereNotNull('fpa_amount')
            ->orWhereNotNull('payroll_fpa')
            ->orderBy('company_name')
            ->get(['id', 'company_name', 'client_code', 'status', 'fpa_amount', 'billing_interval', 'payroll_fpa', 'payroll_billing_interval']);

        $filename = 'fixed-prices-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($clients) {
            $out = fopen('php://output', 'w');

            fputcsv($out, ['Client Code', 'Company Name', 'Status', 'FPA Amount', 'Billing Interval', 'Payroll FPA', 'Payroll Interval', 'Client Total']);

            foreach ($clients as $c) {
                fputcsv($out, [
                    $c->client_code,
                    $c->company_name,
                    ucfirst($c->status),
                    $c->fpa_amount,
                    $c->billing_interval ? ucfirst($c->billing_interval) : '',
                    $c->payroll_fpa,
                    $c->payroll_billing_interval ? ucfirst($c->payroll_billing_interval) : '',
                    ($c->fpa_amount ?? 0) + ($c->payroll_fpa ?? 0),
                ]);
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
