<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Renewal extends Model
{
    protected $fillable = [
        'client_id', 'description', 'completed_date', 'due_date', 'status', 'notes',
    ];

    protected $casts = [
        'completed_date' => 'date',
        'due_date'       => 'date',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'sent'    => 'info',
            'signed'  => 'success',
            'overdue' => 'danger',
            default   => 'warning',
        };
    }

    public function getIsOverdueAttribute(): bool
    {
        return in_array($this->status, ['pending', 'sent']) && $this->due_date?->isPast();
    }
}
