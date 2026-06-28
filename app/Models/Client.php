<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = [
        'client_code', 'company_name', 'client_type_id', 'contact_name', 'email', 'phone',
        'address', 'town', 'county', 'postcode',
        'vat_number', 'company_number', 'utr_number', 'paye_ref',
        'status', 'account_manager', 'notes',
        'fpa_year_end', 'fpa_amount', 'billing_interval',
        'sa_billed_separately', 'payroll_invoiced_separately',
        'payroll_fpa', 'payroll_billing_interval',
        'payment_method',
    ];

    protected $casts = [
        'fpa_year_end'              => 'date',
        'sa_billed_separately'      => 'boolean',
        'payroll_invoiced_separately' => 'boolean',
        'fpa_amount'                => 'decimal:2',
        'payroll_fpa'               => 'decimal:2',
    ];

    public function clientType(): BelongsTo
    {
        return $this->belongsTo(ClientType::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'client_service')
            ->withPivot('price_override', 'start_date', 'end_date', 'notes')
            ->withTimestamps();
    }

    public function billingLines(): HasMany
    {
        return $this->hasMany(ClientBillingLine::class)->orderBy('id');
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class);
    }

    public function renewals(): HasMany
    {
        return $this->hasMany(Renewal::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'active'   => 'success',
            'inactive' => 'secondary',
            'prospect' => 'warning',
            default    => 'secondary',
        };
    }
}
