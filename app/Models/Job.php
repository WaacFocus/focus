<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Job extends Model
{
    protected $table = 'practice_jobs';

    protected $fillable = [
        'name', 'description', 'client_id', 'service_id', 'assigned_to',
        'frequency', 'due_date', 'status', 'completed_at', 'notes',
    ];

    protected $casts = [
        'due_date'     => 'date',
        'completed_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    // Per-request cache of all job statuses keyed as "service_id|slug" and "global|slug"
    private static ?array $statusMap = null;

    private static function statusMap(): array
    {
        if (static::$statusMap === null) {
            static::$statusMap = [];
            JobStatus::where('is_active', true)->orderBy('sort_order')->get()
                ->each(function ($js) {
                    $key = ($js->service_id ? (string) $js->service_id : 'global') . '|' . $js->slug;
                    static::$statusMap[$key] = $js;
                });
        }
        return static::$statusMap;
    }

    private function resolvedStatus(): ?JobStatus
    {
        $map = static::statusMap();
        $svc = $this->service_id ? (string) $this->service_id : 'global';
        return $map[$svc . '|' . $this->status] ?? $map['global|' . $this->status] ?? null;
    }

    public function getStatusBadgeAttribute(): string
    {
        return $this->resolvedStatus()?->color ?? 'secondary';
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->resolvedStatus()?->name ?? ucfirst(str_replace('_', ' ', $this->status));
    }

    public function isComplete(): bool
    {
        return (bool) ($this->resolvedStatus()?->is_completion ?? false);
    }

    public function getFrequencyLabelAttribute(): string
    {
        return match ($this->frequency) {
            'weekly'    => 'Weekly',
            'monthly'   => 'Monthly',
            'quarterly' => 'Quarterly',
            'yearly'    => 'Yearly',
            'one-off'   => 'One-off',
            default   => ucfirst($this->frequency),
        };
    }

    public function scheduleNext(): ?self
    {
        if ($this->frequency === 'one-off') {
            return null;
        }

        $nextDue = match ($this->frequency) {
            'weekly'    => $this->due_date->copy()->addWeek(),
            'monthly'   => $this->due_date->copy()->addMonth(),
            'quarterly' => $this->due_date->copy()->addMonths(3),
            'yearly'    => $this->due_date->copy()->addYear(),
        };

        // Find the first non-completion status for the next job (default 'pending')
        $nextStatus = JobStatus::forService($this->service_id)
            ->where('is_completion', false)
            ->first()?->slug ?? 'pending';

        return self::create([
            'name'        => $this->name,
            'description' => $this->description,
            'client_id'   => $this->client_id,
            'service_id'  => $this->service_id,
            'assigned_to' => $this->assigned_to,
            'frequency'   => $this->frequency,
            'due_date'    => $nextDue,
            'status'      => $nextStatus,
            'notes'       => $this->notes,
        ]);
    }
}
