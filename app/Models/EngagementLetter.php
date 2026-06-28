<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EngagementLetter extends Model
{
    protected $fillable = [
        'client_id', 'renewal_id', 'sent_by', 'subject', 'sections',
        'composed_html', 'token', 'status', 'sent_at', 'signed_at',
        'signed_name', 'signed_ip',
    ];

    protected $casts = [
        'sections'  => 'array',
        'sent_at'   => 'datetime',
        'signed_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function renewal(): BelongsTo
    {
        return $this->belongsTo(Renewal::class);
    }

    public function sentBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'sent'   => 'info',
            'signed' => 'success',
            default  => 'secondary',
        };
    }
}
