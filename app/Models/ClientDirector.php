<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientDirector extends Model
{
    protected $fillable = [
        'client_id', 'name', 'role', 'appointed_on', 'resigned_on',
        'dob_month', 'dob_year', 'nationality', 'occupation', 'country_of_residence', 'sa_required',
    ];

    protected $casts = [
        'appointed_on' => 'date',
        'resigned_on'  => 'date',
        'sa_required'  => 'boolean',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function getRoleLabel(): string
    {
        return ucwords(str_replace('-', ' ', $this->role));
    }
}
