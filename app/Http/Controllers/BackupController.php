<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientType;
use App\Models\Job;
use App\Models\Renewal;
use App\Models\Service;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupController extends Controller
{
    // ── Column definitions ────────────────────────────────────────────────────

    private array $schemas = [
        'clients' => [
            'label'   => 'Clients',
            'icon'    => 'bi-people',
            'headers' => [
                'client_code','company_name','client_type','contact_name','email','phone',
                'address','town','county','postcode','status','account_manager','notes',
                'vat_number','company_number','utr_number','paye_ref',
                'fpa_amount','billing_interval','fpa_year_end','payment_method',
                'payroll_fpa','payroll_billing_interval','sa_billed_separately','payroll_invoiced_separately',
            ],
            'notes' => 'client_type must match an existing Client Type name. status: active|inactive|prospect. billing_interval / payroll_billing_interval: monthly|quarterly|annually|one-off. sa_billed_separately / payroll_invoiced_separately: 1 or 0. Rows are matched on client_code — existing records will be updated.',
        ],
        'jobs' => [
            'label'   => 'Jobs',
            'icon'    => 'bi-briefcase',
            'headers' => ['name','description','client_code','assigned_to_email','frequency','due_date','status','notes'],
            'notes'   => 'client_code must match an existing client. assigned_to_email must match an existing user. frequency: weekly|monthly|quarterly|yearly|one-off. status: pending|in_progress|completed. due_date: YYYY-MM-DD. Rows are always inserted as new records.',
        ],
        'renewals' => [
            'label'   => 'Renewals',
            'icon'    => 'bi-arrow-repeat',
            'headers' => ['client_code','service_name','description','renewal_date','amount','status','billing_cycle','next_renewal_date','notes'],
            'notes'   => 'client_code must match an existing client. service_name must match an existing service (or leave blank). status: pending|renewed|cancelled|overdue. billing_cycle: monthly|quarterly|annually|one-off. Dates: YYYY-MM-DD. Rows are always inserted as new records.',
        ],
        'tasks' => [
            'label'   => 'Tasks',
            'icon'    => 'bi-check2-square',
            'headers' => ['name','description','status','priority','is_urgent','due_date'],
            'notes'   => 'status: pending|in_progress|completed|cancelled. priority: low|medium|high. is_urgent: 1 or 0. due_date: YYYY-MM-DD. Rows are always inserted as new records.',
        ],
    ];

    // ── Page ──────────────────────────────────────────────────────────────────

    public function index()
    {
        $counts = [
            'clients'  => Client::count(),
            'jobs'     => Job::count(),
            'renewals' => Renewal::count(),
            'tasks'    => Task::count(),
        ];
        return view('admin.backup', ['schemas' => $this->schemas, 'counts' => $counts]);
    }

    // ── Export ────────────────────────────────────────────────────────────────

    public function export(string $type): StreamedResponse
    {
        abort_unless(array_key_exists($type, $this->schemas), 404);

        $filename = $type . '-export-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($type) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $this->schemas[$type]['headers']);

            match($type) {
                'clients'  => $this->exportClients($out),
                'jobs'     => $this->exportJobs($out),
                'renewals' => $this->exportRenewals($out),
                'tasks'    => $this->exportTasks($out),
            };

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    private function exportClients($out): void
    {
        Client::with('clientType')->orderBy('company_name')->chunk(200, function ($rows) use ($out) {
            foreach ($rows as $c) {
                fputcsv($out, [
                    $c->client_code, $c->company_name, $c->clientType?->name ?? '',
                    $c->contact_name, $c->email, $c->phone,
                    $c->address, $c->town, $c->county, $c->postcode,
                    $c->status, $c->account_manager, $c->notes,
                    $c->vat_number, $c->company_number, $c->utr_number, $c->paye_ref,
                    $c->fpa_amount, $c->billing_interval,
                    $c->fpa_year_end?->format('Y-m-d') ?? '',
                    $c->payment_method, $c->payroll_fpa, $c->payroll_billing_interval,
                    $c->sa_billed_separately ? '1' : '0',
                    $c->payroll_invoiced_separately ? '1' : '0',
                ]);
            }
        });
    }

    private function exportJobs($out): void
    {
        Job::with(['client', 'assignedTo'])->orderBy('due_date')->chunk(200, function ($rows) use ($out) {
            foreach ($rows as $j) {
                fputcsv($out, [
                    $j->name, $j->description, $j->client?->client_code ?? '',
                    $j->assignedTo?->email ?? '',
                    $j->frequency, $j->due_date->format('Y-m-d'),
                    $j->status, $j->notes,
                ]);
            }
        });
    }

    private function exportRenewals($out): void
    {
        Renewal::with(['client', 'service'])->orderBy('renewal_date')->chunk(200, function ($rows) use ($out) {
            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->client?->client_code ?? '', $r->service?->name ?? '',
                    $r->description, $r->renewal_date->format('Y-m-d'),
                    $r->amount, $r->status, $r->billing_cycle,
                    $r->next_renewal_date?->format('Y-m-d') ?? '',
                    $r->notes,
                ]);
            }
        });
    }

    private function exportTasks($out): void
    {
        Task::orderBy('due_date')->chunk(200, function ($rows) use ($out) {
            foreach ($rows as $t) {
                fputcsv($out, [
                    $t->name, $t->description, $t->status, $t->priority,
                    $t->is_urgent ? '1' : '0',
                    $t->due_date?->format('Y-m-d') ?? '',
                ]);
            }
        });
    }

    // ── Example templates ─────────────────────────────────────────────────────

    public function template(string $type): StreamedResponse
    {
        abort_unless(array_key_exists($type, $this->schemas), 404);

        $filename = $type . '-template.csv';
        $examples = $this->exampleRows();

        return response()->streamDownload(function () use ($type, $examples) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $this->schemas[$type]['headers']);
            foreach ($examples[$type] as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    private function exampleRows(): array
    {
        return [
            'clients' => [
                ['CLT001','Acme Ltd','Limited Company','Jane Smith','jane@acme.co.uk','01234 567890','1 High Street','London','','EC1A 1BB','active','David Woods','Key client','GB123456789','12345678','','123/AB456','1200.00','monthly','2025-03-31','Direct Debit','150.00','monthly','0','0'],
                ['CLT002','Smith & Sons','Partnership','Bob Smith','bob@smithsons.co.uk','','','Birmingham','West Midlands','B1 1AA','active','','','','','9876543210','','0','','','','0','','0','0'],
            ],
            'jobs' => [
                ['Prepare Annual Accounts','Year-end accounts for 2024/25','CLT001','david@waac.co.uk','yearly','2025-03-31','pending',''],
                ['Payroll Run','Monthly payroll for 5 employees','CLT001','david@waac.co.uk','monthly','2025-01-31','in_progress',''],
            ],
            'renewals' => [
                ['CLT001','Bookkeeping','Monthly bookkeeping service','2025-06-01','150.00','pending','monthly','2025-07-01','Auto-renews monthly'],
                ['CLT002','VAT Returns','Quarterly VAT return','2025-06-30','250.00','pending','quarterly','2025-09-30',''],
            ],
            'tasks' => [
                ['Review client files','Annual file review for all active clients','pending','high','1','2025-02-28'],
                ['Update software','Install latest accounting software update','pending','medium','0','2025-01-15'],
            ],
        ];
    }

    // ── Import ────────────────────────────────────────────────────────────────

    public function import(Request $request)
    {
        $request->validate([
            'type' => 'required|in:clients,jobs,renewals,tasks',
            'file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $type    = $request->input('type');
        $path    = $request->file('file')->getRealPath();
        $handle  = fopen($path, 'r');
        $headers = array_map('trim', fgetcsv($handle));

        $expected = $this->schemas[$type]['headers'];
        $missing  = array_diff($expected, $headers);

        if ($missing) {
            fclose($handle);
            return back()->withErrors(['file' => 'CSV is missing columns: ' . implode(', ', $missing)])->withInput();
        }

        $colIndex = array_flip($headers);
        $get      = fn($row, $col) => trim($colIndex[$col] !== null ? ($row[$colIndex[$col]] ?? '') : '');

        $created = $updated = $skipped = 0;
        $errors  = [];
        $line    = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $line++;
            if (count(array_filter($row)) === 0) continue;

            try {
                $result = match($type) {
                    'clients'  => $this->importClientRow($row, $get),
                    'jobs'     => $this->importJobRow($row, $get),
                    'renewals' => $this->importRenewalRow($row, $get),
                    'tasks'    => $this->importTaskRow($row, $get),
                };

                if ($result === 'created') $created++;
                elseif ($result === 'updated') $updated++;
                else $skipped++;

            } catch (\Throwable $e) {
                $errors[] = "Row {$line}: " . $e->getMessage();
                $skipped++;
            }
        }

        fclose($handle);

        $summary = "Import complete — {$created} created, {$updated} updated, {$skipped} skipped.";
        if ($errors) {
            $summary .= ' ' . count($errors) . ' row(s) had errors.';
            session()->flash('import_errors', array_slice($errors, 0, 20));
        }

        return back()->with('success', $summary);
    }

    private function importClientRow(array $row, callable $get): string
    {
        $code = $get($row, 'client_code');
        if (!$code) throw new \Exception('client_code is required');

        $typeName = $get($row, 'client_type');
        $typeId   = $typeName
            ? ClientType::where('name', $typeName)->value('id')
            : null;

        $data = [
            'company_name'               => $get($row, 'company_name') ?: throw new \Exception('company_name is required'),
            'client_type_id'             => $typeId,
            'contact_name'               => $get($row, 'contact_name') ?: null,
            'email'                      => $get($row, 'email') ?: null,
            'phone'                      => $get($row, 'phone') ?: null,
            'address'                    => $get($row, 'address') ?: null,
            'town'                       => $get($row, 'town') ?: null,
            'county'                     => $get($row, 'county') ?: null,
            'postcode'                   => $get($row, 'postcode') ?: null,
            'status'                     => $get($row, 'status') ?: 'active',
            'account_manager'            => $get($row, 'account_manager') ?: null,
            'notes'                      => $get($row, 'notes') ?: null,
            'vat_number'                 => $get($row, 'vat_number') ?: null,
            'company_number'             => $get($row, 'company_number') ?: null,
            'utr_number'                 => $get($row, 'utr_number') ?: null,
            'paye_ref'                   => $get($row, 'paye_ref') ?: null,
            'fpa_amount'                 => $get($row, 'fpa_amount') ?: null,
            'billing_interval'           => $get($row, 'billing_interval') ?: null,
            'fpa_year_end'               => $get($row, 'fpa_year_end') ?: null,
            'payment_method'             => $get($row, 'payment_method') ?: null,
            'payroll_fpa'                => $get($row, 'payroll_fpa') ?: null,
            'payroll_billing_interval'   => $get($row, 'payroll_billing_interval') ?: null,
            'sa_billed_separately'       => (bool) $get($row, 'sa_billed_separately'),
            'payroll_invoiced_separately' => (bool) $get($row, 'payroll_invoiced_separately'),
        ];

        $existing = Client::where('client_code', $code)->first();
        if ($existing) {
            $existing->update($data);
            return 'updated';
        }

        Client::create(array_merge($data, ['client_code' => $code]));
        return 'created';
    }

    private function importJobRow(array $row, callable $get): string
    {
        $clientCode = $get($row, 'client_code');
        $client     = $clientCode ? Client::where('client_code', $clientCode)->first() : null;
        if ($clientCode && !$client) throw new \Exception("Client '{$clientCode}' not found");

        $assigneeEmail = $get($row, 'assigned_to_email');
        $assignee      = $assigneeEmail ? User::where('email', $assigneeEmail)->value('id') : null;

        $name = $get($row, 'name') ?: throw new \Exception('name is required');

        Job::create([
            'name'        => $name,
            'description' => $get($row, 'description') ?: null,
            'client_id'   => $client?->id,
            'assigned_to' => $assignee,
            'frequency'   => $get($row, 'frequency') ?: 'one-off',
            'due_date'    => $get($row, 'due_date') ?: now()->toDateString(),
            'status'      => $get($row, 'status') ?: 'pending',
            'notes'       => $get($row, 'notes') ?: null,
        ]);

        return 'created';
    }

    private function importRenewalRow(array $row, callable $get): string
    {
        $clientCode = $get($row, 'client_code');
        $client     = $clientCode ? Client::where('client_code', $clientCode)->first() : null;
        if ($clientCode && !$client) throw new \Exception("Client '{$clientCode}' not found");

        $serviceName = $get($row, 'service_name');
        $serviceId   = $serviceName ? Service::where('name', $serviceName)->value('id') : null;

        $renewalDate = $get($row, 'renewal_date') ?: throw new \Exception('renewal_date is required');

        Renewal::create([
            'client_id'         => $client?->id,
            'service_id'        => $serviceId,
            'description'       => $get($row, 'description') ?: null,
            'renewal_date'      => $renewalDate,
            'amount'            => $get($row, 'amount') ?: null,
            'status'            => $get($row, 'status') ?: 'pending',
            'billing_cycle'     => $get($row, 'billing_cycle') ?: null,
            'next_renewal_date' => $get($row, 'next_renewal_date') ?: null,
            'notes'             => $get($row, 'notes') ?: null,
        ]);

        return 'created';
    }

    private function importTaskRow(array $row, callable $get): string
    {
        $name = $get($row, 'name') ?: throw new \Exception('name is required');

        Task::create([
            'name'        => $name,
            'description' => $get($row, 'description') ?: null,
            'status'      => $get($row, 'status') ?: 'pending',
            'priority'    => $get($row, 'priority') ?: 'medium',
            'is_urgent'   => (bool) $get($row, 'is_urgent'),
            'due_date'    => $get($row, 'due_date') ?: null,
        ]);

        return 'created';
    }
}
