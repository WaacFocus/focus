<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    protected $fillable = [
        'client_id', 'name', 'description', 'status',
        'start_date', 'end_date', 'budget', 'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'decimal:2',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'project_products')
            ->withPivot('quantity', 'unit_price', 'notes')
            ->withTimestamps();
    }

    public function getTotalCostAttribute(): float
    {
        return $this->products->sum(fn ($p) => $p->pivot->quantity * $p->pivot->unit_price);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'quote'     => 'info',
            'active'    => 'success',
            'on_hold'   => 'warning',
            'completed' => 'primary',
            'cancelled' => 'danger',
            default     => 'secondary',
        };
    }
}
