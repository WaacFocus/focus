<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Job;
use App\Models\Renewal;
use App\Models\SavedReport;
use App\Models\Task;
use App\Models\User;
use App\Services\Smtp2goService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportBuilderController extends Controller
{
    // ── Source / column definitions ───────────────────────────────────────────

    private function sources(): array
    {
        return [
            'clients' => [
                'label'   => 'Clients',
                'icon'    => 'bi-people',
                'columns' => [
                    'company_name' => 'Company Name',
                    'contact_name' => 'Contact Name',
                    'email'        => 'Email',
                    'phone'        => 'Phone',
                    'status'       => 'Status',
                    'created_at'   => 'Date Added',
                ],
                'filter_fields' => [
                    'company_name' => ['label' => 'Company Name', 'type' => 'text'],
                    'status'       => ['label' => 'Status',       'type' => 'select', 'options' => ['active' => 'Active', 'inactive' => 'Inactive']],
                    'created_at'   => ['label' => 'Date Added',   'type' => 'date'],
                ],
            ],
            'jobs' => [
                'label'   => 'Jobs',
                'icon'    => 'bi-briefcase',
                'columns' => [
                    'name'        => 'Job Name',
                    'client'      => 'Client',
                    'assigned_to' => 'Assigned To',
                    'frequency'   => 'Frequency',
                    'due_date'    => 'Due Date',
                    'status'      => 'Status',
                    'completed_at'=> 'Completed At',
                ],
                'filter_fields' => [
                    'name'      => ['label' => 'Job Name',    'type' => 'text'],
                    'status'    => ['label' => 'Status',      'type' => 'select', 'options' => ['pending' => 'Pending', 'in_progress' => 'In Progress', 'completed' => 'Completed']],
                    'frequency' => ['label' => 'Frequency',   'type' => 'select', 'options' => ['weekly' => 'Weekly', 'monthly' => 'Monthly', 'quarterly' => 'Quarterly', 'yearly' => 'Yearly', 'one-off' => 'One-off']],
                    'due_date'  => ['label' => 'Due Date',    'type' => 'date'],
                ],
            ],
            'tasks' => [
                'label'   => 'Tasks',
                'icon'    => 'bi-check2-square',
                'columns' => [
                    'name'      => 'Task Name',
                    'priority'  => 'Priority',
                    'status'    => 'Status',
                    'due_date'  => 'Due Date',
                    'is_urgent' => 'Urgent',
                ],
                'filter_fields' => [
                    'name'     => ['label' => 'Task Name', 'type' => 'text'],
                    'status'   => ['label' => 'Status',    'type' => 'select', 'options' => ['pending' => 'Pending', 'in_progress' => 'In Progress', 'completed' => 'Completed']],
                    'priority' => ['label' => 'Priority',  'type' => 'select', 'options' => ['low' => 'Low', 'medium' => 'Medium', 'high' => 'High']],
                    'due_date' => ['label' => 'Due Date',  'type' => 'date'],
                ],
            ],
            'renewals' => [
                'label'   => 'Renewals',
                'icon'    => 'bi-arrow-repeat',
                'columns' => [
                    'client'       => 'Client',
                    'description'  => 'Description',
                    'renewal_date' => 'Renewal Date',
                    'amount'       => 'Amount',
                    'status'       => 'Status',
                ],
                'filter_fields' => [
                    'status'       => ['label' => 'Status',       'type' => 'select', 'options' => ['pending' => 'Pending', 'completed' => 'Completed']],
                    'renewal_date' => ['label' => 'Renewal Date', 'type' => 'date'],
                    'amount'       => ['label' => 'Amount',       'type' => 'number'],
                ],
            ],
        ];
    }

    // ── CRUD ──────────────────────────────────────────────────────────────────

    public function index()
    {
        $savedReports = SavedReport::orderBy('name')->get();
        return view('reports.custom.index', compact('savedReports'));
    }

    public function create()
    {
        $sources = $this->sources();
        return view('reports.custom.builder', compact('sources'));
    }

    public function edit(SavedReport $savedReport)
    {
        $sources = $this->sources();
        return view('reports.custom.builder', ['sources' => $sources, 'savedReport' => $savedReport]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:100',
            'config' => 'required|array',
            'config.source'  => 'required|in:' . implode(',', array_keys($this->sources())),
            'config.columns' => 'required|array|min:1',
        ]);

        SavedReport::create([
            'user_id' => Auth::id(),
            'name'    => $request->name,
            'config'  => $request->config,
        ]);

        return response()->json(['message' => 'Report saved.']);
    }

    public function update(Request $request, SavedReport $savedReport)
    {
        $request->validate([
            'name'   => 'required|string|max:100',
            'config' => 'required|array',
            'config.source'  => 'required|in:' . implode(',', array_keys($this->sources())),
            'config.columns' => 'required|array|min:1',
        ]);

        $savedReport->update([
            'name'   => $request->name,
            'config' => $request->config,
        ]);

        return response()->json(['message' => 'Report updated.']);
    }

    public function destroy(SavedReport $savedReport)
    {
        $savedReport->delete();
        return redirect()->route('reports.custom.index')->with('success', 'Report deleted.');
    }

    // ── Preview (AJAX) ─────────────────────────────────────────────────────────

    public function preview(Request $request)
    {
        $config = $request->validate([
            'source'    => 'required|in:' . implode(',', array_keys($this->sources())),
            'columns'   => 'required|array|min:1',
            'filters'   => 'nullable|array',
            'sort_by'   => 'nullable|string',
            'sort_dir'  => 'nullable|in:asc,desc',
        ]);

        $result = $this->executeReport($config);
        return response()->json($result);
    }

    // ── Run saved report ───────────────────────────────────────────────────────

    public function run(SavedReport $savedReport)
    {
        $sources = $this->sources();
        $result  = $this->executeReport($savedReport->config);
        $users   = User::orderBy('name')->get();
        return view('reports.custom.run', compact('savedReport', 'result', 'sources', 'users'));
    }

    public function csv(SavedReport $savedReport): StreamedResponse
    {
        $result   = $this->executeReport($savedReport->config);
        $slug     = str($savedReport->name)->slug();
        $filename = "{$slug}-" . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($result) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $result['headers']);
            foreach ($result['rows'] as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function pdf(SavedReport $savedReport, string $orientation = 'landscape')
    {
        $result   = $this->executeReport($savedReport->config);
        $slug     = str($savedReport->name)->slug();
        $filename = "{$slug}-" . now()->format('Y-m-d') . "-{$orientation}.pdf";

        $pdf = Pdf::loadView('reports.pdf.custom', compact('savedReport', 'result', 'orientation'))
            ->setPaper('A4', $orientation);

        return $pdf->download($filename);
    }

    public function email(Request $request, SavedReport $savedReport, Smtp2goService $smtp2go)
    {
        $request->validate([
            'user_ids'   => ['required', 'array', 'min:1'],
            'user_ids.*' => ['exists:users,id'],
        ]);

        $result  = $this->executeReport($savedReport->config);
        $subject = $savedReport->name . ' — ' . now()->format('d F Y');
        $html    = view('emails.custom-report', compact('savedReport', 'result'))->render();
        $users   = User::whereIn('id', $request->user_ids)->get();

        $sent = $failed = 0;
        foreach ($users as $user) {
            $smtp2go->send($user->email, $user->name, $subject, $html) ? $sent++ : $failed++;
        }

        if ($sent > 0 && $failed === 0) {
            return back()->with('success', "Report emailed to {$sent} " . str('user')->plural($sent) . '.');
        }
        if ($sent > 0) {
            return back()->with('success', "Report emailed to {$sent} " . str('user')->plural($sent) . " ({$failed} failed).");
        }
        return back()->with('error', 'Failed to send the report. Please check the SMTP2GO configuration.');
    }

    // ── Query engine ───────────────────────────────────────────────────────────

    private function executeReport(array $config): array
    {
        $source  = $config['source'];
        $cols    = $config['columns'] ?? [];
        $filters = $config['filters'] ?? [];
        $sortBy  = $config['sort_by'] ?? null;
        $sortDir = $config['sort_dir'] ?? 'asc';

        $sources = $this->sources();
        $headers = array_values(array_intersect_key($sources[$source]['columns'], array_flip($cols)));

        $rows = match($source) {
            'clients'  => $this->queryClients($cols, $filters, $sortBy, $sortDir),
            'jobs'     => $this->queryJobs($cols, $filters, $sortBy, $sortDir),
            'tasks'    => $this->queryTasks($cols, $filters, $sortBy, $sortDir),
            'renewals' => $this->queryRenewals($cols, $filters, $sortBy, $sortDir),
        };

        return ['headers' => $headers, 'rows' => $rows, 'count' => count($rows)];
    }

    private function applyFilters($query, array $filters, string $table): object
    {
        foreach ($filters as $f) {
            $field    = $f['field']    ?? null;
            $operator = $f['operator'] ?? null;
            $value    = $f['value']    ?? null;

            if (!$field || !$operator) continue;

            $col = "{$table}.{$field}";

            match($operator) {
                'equals'       => $query->where($col, $value),
                'not_equals'   => $query->where($col, '!=', $value),
                'contains'     => $query->where($col, 'like', "%{$value}%"),
                'starts_with'  => $query->where($col, 'like', "{$value}%"),
                'greater_than' => $query->where($col, '>', $value),
                'less_than'    => $query->where($col, '<', $value),
                'is_empty'     => $query->whereNull($col)->orWhere($col, ''),
                'is_not_empty' => $query->whereNotNull($col)->where($col, '!=', ''),
                default        => null,
            };
        }
        return $query;
    }

    private function queryClients(array $cols, array $filters, ?string $sortBy, string $sortDir): array
    {
        $q = Client::query();
        $this->applyFilters($q, $filters, 'clients');
        $sortCol = in_array($sortBy, ['company_name','contact_name','email','phone','status','created_at']) ? $sortBy : 'company_name';
        $q->orderBy($sortCol, $sortDir);

        return $q->get()->map(fn($c) => $this->pickCols([
            'company_name' => $c->company_name,
            'contact_name' => $c->contact_name,
            'email'        => $c->email,
            'phone'        => $c->phone,
            'status'       => ucfirst($c->status),
            'created_at'   => $c->created_at?->format('d M Y'),
        ], $cols))->toArray();
    }

    private function queryJobs(array $cols, array $filters, ?string $sortBy, string $sortDir): array
    {
        $q = Job::with(['client', 'assignedTo']);
        $this->applyFilters($q, $filters, 'practice_jobs');
        $sortCol = in_array($sortBy, ['name','frequency','due_date','status','completed_at']) ? $sortBy : 'due_date';
        $q->orderBy($sortCol, $sortDir);

        return $q->get()->map(fn($j) => $this->pickCols([
            'name'         => $j->name,
            'client'       => $j->client?->company_name ?? '—',
            'assigned_to'  => $j->assignedTo?->name ?? '—',
            'frequency'    => $j->frequency_label,
            'due_date'     => $j->due_date->format('d M Y'),
            'status'       => ucfirst(str_replace('_', ' ', $j->status)),
            'completed_at' => $j->completed_at?->format('d M Y') ?? '—',
        ], $cols))->toArray();
    }

    private function queryTasks(array $cols, array $filters, ?string $sortBy, string $sortDir): array
    {
        $q = Task::query();
        $this->applyFilters($q, $filters, 'tasks');
        $sortCol = in_array($sortBy, ['name','priority','status','due_date','is_urgent']) ? $sortBy : 'due_date';
        $q->orderBy($sortCol, $sortDir);

        return $q->get()->map(fn($t) => $this->pickCols([
            'name'      => $t->name,
            'priority'  => ucfirst($t->priority),
            'status'    => ucfirst(str_replace('_', ' ', $t->status)),
            'due_date'  => $t->due_date?->format('d M Y') ?? '—',
            'is_urgent' => $t->is_urgent ? 'Yes' : 'No',
        ], $cols))->toArray();
    }

    private function queryRenewals(array $cols, array $filters, ?string $sortBy, string $sortDir): array
    {
        $q = Renewal::with('client');
        $this->applyFilters($q, $filters, 'renewals');
        $sortCol = in_array($sortBy, ['description','renewal_date','amount','status']) ? $sortBy : 'renewal_date';
        $q->orderBy($sortCol, $sortDir);

        return $q->get()->map(fn($r) => $this->pickCols([
            'client'       => $r->client?->company_name ?? '—',
            'description'  => $r->description,
            'renewal_date' => $r->renewal_date->format('d M Y'),
            'amount'       => $r->amount ? '£' . number_format($r->amount, 2) : '—',
            'status'       => ucfirst($r->status),
        ], $cols))->toArray();
    }

    private function pickCols(array $row, array $cols): array
    {
        return array_values(array_map(fn($col) => $row[$col] ?? '—', $cols));
    }
}
