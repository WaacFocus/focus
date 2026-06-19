<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = [
        'name', 'description', 'default_price', 'billing_cycle', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'default_price' => 'decimal:2',
    ];

    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class, 'client_service')
            ->withPivot('price_override', 'start_date', 'end_date', 'notes')
            ->withTimestamps();
    }

    public function renewals(): HasMany
    {
        return $this->hasMany(Renewal::class);
    }

    public function getBillingCycleLabelAttribute(): string
    {
        return match ($this->billing_cycle) {
            'monthly'   => 'Monthly',
            'quarterly' => 'Quarterly',
            'annually'  => 'Annually',
            'one_off'   => 'One-off',
            default     => $this->billing_cycle,
        };
    }
}
