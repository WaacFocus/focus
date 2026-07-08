<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class JobStatus extends Model
{
    protected $fillable = ['service_id', 'name', 'slug', 'color', 'sort_order', 'is_completion', 'is_active'];

    protected $casts = [
        'is_completion' => 'boolean',
        'is_active'     => 'boolean',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Statuses valid for a given service_id: service-specific ones if defined, else global.
     */
    public static function forService(?int $serviceId): Collection
    {
        if ($serviceId) {
            $serviceStatuses = static::where('service_id', $serviceId)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();
            if ($serviceStatuses->isNotEmpty()) {
                return $serviceStatuses;
            }
        }
        return static::whereNull('service_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    public function getColorLabelAttribute(): string
    {
        return match ($this->color) {
            'secondary'   => 'Grey',
            'in-progress' => 'Blue',
            'success'     => 'Green',
            'warning'     => 'Yellow',
            'danger'      => 'Red',
            'info'        => 'Cyan',
            'primary'     => 'Teal',
            'dark'        => 'Dark',
            default       => ucfirst($this->color),
        };
    }
}
