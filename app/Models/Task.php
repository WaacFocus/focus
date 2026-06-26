<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'name', 'description', 'status', 'priority', 'is_urgent', 'due_date', 'completed_at',
    ];

    protected $casts = [
        'due_date'    => 'date',
        'completed_at'=> 'datetime',
        'is_urgent'   => 'boolean',
    ];

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending'     => 'secondary',
            'in_progress' => 'primary',
            'completed'   => 'success',
            'cancelled'   => 'danger',
            default       => 'secondary',
        };
    }

    public function getPriorityBadgeAttribute(): string
    {
        return match ($this->priority) {
            'high'   => 'danger',
            'medium' => 'warning',
            'low'    => 'success',
            default  => 'secondary',
        };
    }
}
