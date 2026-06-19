<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Renewal extends Model
{
    protected $fillable = [
        'client_id', 'service_id', 'description', 'renewal_date',
        'amount', 'status', 'billing_cycle', 'next_renewal_date', 'notes',
    ];

    protected $casts = [
        'renewal_date' => 'date',
        'next_renewal_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending'   => 'warning',
            'renewed'   => 'success',
            'cancelled' => 'secondary',
            'overdue'   => 'danger',
            default     => 'secondary',
        };
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->status === 'pending' && $this->renewal_date->isPast();
    }
}
