<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'event',
        'description',
        'url',
        'method',
        'ip_address',
        'user_agent',
        'session_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function record(string $event, string $description, array $extra = []): void
    {
        if (!auth()->check()) {
            return;
        }

        $request = request();

        static::create(array_merge([
            'user_id'    => auth()->id(),
            'event'      => $event,
            'description'=> $description,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'session_id' => session()->getId(),
        ], $extra));
    }
}
