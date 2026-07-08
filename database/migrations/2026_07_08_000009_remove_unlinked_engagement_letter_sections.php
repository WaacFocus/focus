<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('services') || !Schema::hasTable('engagement_letter_templates')) {
            return;
        }

        // All service names, lower-cased, as the authoritative list
        $serviceTypes = DB::table('services')
            ->pluck('name')
            ->map(fn($n) => strtolower($n))
            ->toArray();

        // Delete non-mandatory sections whose service_type is null, empty,
        // or doesn't match any current service name
        DB::table('engagement_letter_templates')
            ->where('is_mandatory', false)
            ->where(function ($q) use ($serviceTypes) {
                $q->whereNull('service_type')
                  ->orWhere('service_type', '')
                  ->orWhereNotIn('service_type', $serviceTypes);
            })
            ->delete();
    }

    public function down(): void
    {
        // Not reversible
    }
};
