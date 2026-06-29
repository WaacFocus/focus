<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = [
        'client_code', 'company_name', 'client_type_id',
        'contact_title', 'contact_first_name', 'contact_last_name',
        'email', 'phone',
        'address', 'town', 'county', 'postcode',
        'vat_number', 'company_number', 'utr_number', 'paye_ref',
        'status', 'account_manager', 'notes',
        'fpa_year_end', 'fpa_amount', 'billing_interval',
        'payment_method',
        'ch_status', 'ch_incorporated_on', 'ch_jurisdiction', 'ch_sic_codes',
        'ch_accounts_year_end', 'ch_accounts_next_due', 'ch_confirmation_statement_next_due',
    ];

    protected $casts = [
        'fpa_year_end'                          => 'date',
        'fpa_amount'                            => 'decimal:2',
        'ch_incorporated_on'                    => 'date',
        'ch_accounts_year_end'                  => 'date',
        'ch_accounts_next_due'                  => 'date',
        'ch_confirmation_statement_next_due'    => 'date',
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

    // Virtual accessor so all existing $client->contact_name reads keep working
    public function getContactNameAttribute(): ?string
    {
        $parts = array_filter([
            $this->contact_title,
            $this->contact_first_name,
            $this->contact_last_name,
        ]);
        return $parts ? implode(' ', $parts) : null;
    }

    // "Dear David," — first name only, falls back to company name
    public function getContactFirstNameGreetingAttribute(): string
    {
        return $this->contact_first_name ?: $this->company_name;
    }

    // Formal salutation: "Mr Smith" — falls back to full contact_name then company
    public function getContactFormalAttribute(): string
    {
        if ($this->contact_last_name) {
            $parts = array_filter([$this->contact_title, $this->contact_last_name]);
            return implode(' ', $parts);
        }
        return $this->contact_name ?: $this->company_name;
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
