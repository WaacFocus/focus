<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Job extends Model
{
    protected $table = 'practice_jobs';

    protected $fillable = [
        'name', 'description', 'client_id', 'assigned_to',
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

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending'     => 'secondary',
            'in_progress' => 'in-progress',
            'completed'   => 'success',
            default       => 'secondary',
        };
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

        return self::create([
            'name'        => $this->name,
            'description' => $this->description,
            'client_id'   => $this->client_id,
            'assigned_to' => $this->assigned_to,
            'frequency'   => $this->frequency,
            'due_date'    => $nextDue,
            'status'      => 'pending',
            'notes'       => $this->notes,
        ]);
    }
}
