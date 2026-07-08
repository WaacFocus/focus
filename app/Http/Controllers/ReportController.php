<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientBillingLine;
use App\Models\Job;
use App\Models\JobStatus;
use App\Models\User;
use App\Services\Smtp2goService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    // ── Upcoming Jobs ──────────────────────────────────────────────────────────

    private function upcomingJobsData(): \Illuminate\Database\Eloquent\Collection
    {
        $completionSlugs = JobStatus::where('is_completion', true)->pluck('slug')->toArray() ?: ['completed'];
        return Job::with(['client', 'assignedTo'])
            ->whereNotIn('status', $completionSlugs)
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
        $users         = User::orderBy('name')->get();

        return view('reports.upcoming-jobs', compact('jobs', 'overdueCount', 'todayCount', 'upcomingCount', 'byUser', 'users'));
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

    // ── Fixed Prices ───────────────────────────────────────────────────────────

    private function fixedPricesData(): \Illuminate\Database\Eloquent\Collection
    {
        return Client::whereNotNull('fpa_amount')
            ->orderBy('company_name')
            ->get(['id', 'company_name', 'client_code', 'status', 'fpa_amount', 'billing_interval']);
    }

    private function fixedPricesMetrics(\Illuminate\Database\Eloquent\Collection $clients): array
    {
        $intervalTotal = function (string $interval) use ($clients): float {
            return (float) $clients->where('billing_interval', $interval)->sum('fpa_amount');
        };

        $monthly   = $intervalTotal('monthly');
        $quarterly = $intervalTotal('quarterly');
        $annual    = $intervalTotal('annually');

        // Additional billing lines
        $lines = ClientBillingLine::all();
        $monthly   += (float) $lines->where('interval', 'monthly')->sum('amount');
        $quarterly += (float) $lines->where('interval', 'quarterly')->sum('amount');
        $annual    += (float) $lines->where('interval', 'annually')->sum('amount');

        // GRF (Gross Recurring Fee): annualised — monthly × 12, quarterly × 4, annual × 1
        $grf = ($monthly * 12) + ($quarterly * 4) + $annual;

        return compact('monthly', 'quarterly', 'annual', 'grf');
    }

    public function fixedPrices()
    {
        $clients         = $this->fixedPricesData();
        $totalFpa   = $clients->sum('fpa_amount');
        $byInterval = $clients->groupBy(fn ($c) => $c->billing_interval ?: 'Unspecified');
        $users      = User::orderBy('name')->get();
        $metrics    = $this->fixedPricesMetrics($clients);

        return view('reports.fixed-prices', compact('clients', 'totalFpa', 'byInterval', 'users', 'metrics'));
    }

    public function fixedPricesPdf(string $orientation = 'portrait')
    {
        $clients  = $this->fixedPricesData();
        $totalFpa = $clients->sum('fpa_amount');
        $metrics  = $this->fixedPricesMetrics($clients);

        $pdf = Pdf::loadView('reports.pdf.fixed-prices', compact('clients', 'totalFpa', 'metrics'))
            ->setPaper('A4', $orientation);

        return $pdf->download('billing-' . now()->format('Y-m-d') . '-' . $orientation . '.pdf');
    }

    public function fixedPricesCsv(): StreamedResponse
    {
        $clients  = $this->fixedPricesData();
        $filename = 'fixed-prices-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($clients) {
            $out = fopen('php://output', 'w');

            fputcsv($out, ['Client Code', 'Company Name', 'Status', 'FPA Amount', 'Billing Interval']);

            foreach ($clients as $c) {
                fputcsv($out, [
                    $c->client_code,
                    $c->company_name,
                    ucfirst($c->status),
                    $c->fpa_amount,
                    $c->billing_interval ? ucfirst($c->billing_interval) : '',
                ]);
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    // ── Email ──────────────────────────────────────────────────────────────────

    public function email(Request $request, Smtp2goService $smtp2go)
    {
        $request->validate([
            'report'     => ['required', 'in:upcoming-jobs,fixed-prices'],
            'user_ids'   => ['required', 'array', 'min:1'],
            'user_ids.*' => ['exists:users,id'],
        ]);

        $users = User::whereIn('id', $request->user_ids)->get();

        if ($request->report === 'upcoming-jobs') {
            $jobs         = $this->upcomingJobsData();
            $overdueCount = $jobs->filter(fn($j) => $j->due_date->isPast() && !$j->due_date->isToday())->count();
            $todayCount   = $jobs->filter(fn($j) => $j->due_date->isToday())->count();
            $byUser       = $jobs->groupBy(fn($j) => $j->assignedTo->name);
            $subject      = 'Upcoming Jobs Report — ' . now()->format('d F Y');
            $html         = view('emails.upcoming-jobs', compact('jobs', 'overdueCount', 'todayCount', 'byUser'))->render();
        } else {
            $clients  = $this->fixedPricesData();
            $totalFpa = $clients->sum('fpa_amount');
            $metrics  = $this->fixedPricesMetrics($clients);
            $subject  = 'Billing Report — ' . now()->format('d F Y');
            $html     = view('emails.fixed-prices', compact('clients', 'totalFpa', 'metrics'))->render();
        }

        $sent   = 0;
        $failed = 0;

        foreach ($users as $user) {
            $smtp2go->send($user->email, $user->name, $subject, $html)
                ? $sent++
                : $failed++;
        }

        if ($sent > 0 && $failed === 0) {
            return back()->with('success', "Report emailed to {$sent} " . str('user')->plural($sent) . '.');
        }

        if ($sent > 0) {
            return back()->with('success', "Report emailed to {$sent} " . str('user')->plural($sent) . " ({$failed} failed — check logs).");
        }

        return back()->with('error', 'Failed to send the report. Please check the SMTP2GO configuration.');
    }
}
