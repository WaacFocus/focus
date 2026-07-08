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

        // Build a map of service names (lower-case) → service id
        $services = DB::table('services')->get(['id', 'name']);
        $serviceMap = $services->keyBy(fn($s) => strtolower($s->name)); // e.g. ['payroll' => obj, ...]

        // Remove templates whose service_type is set to a specific service name
        // that no longer has a matching service — but never touch 'general' sections
        // or mandatory sections, as those are core letter content not service-specific.
        if ($serviceMap->isNotEmpty()) {
            DB::table('engagement_letter_templates')
                ->whereNotNull('service_type')
                ->where('service_type', '!=', '')
                ->where('service_type', '!=', 'general')
                ->where('is_mandatory', false)
                ->whereNotIn('service_type', $serviceMap->keys()->toArray())
                ->delete();
        }

        // Seed a starter template for any service that doesn't have one
        $maxSort = (int) DB::table('engagement_letter_templates')->max('sort_order');

        foreach ($services as $service) {
            $serviceType = strtolower($service->name);

            $exists = DB::table('engagement_letter_templates')
                ->where('service_type', $serviceType)
                ->exists();

            if (!$exists) {
                $maxSort++;
                DB::table('engagement_letter_templates')->insert([
                    'title'            => $service->name,
                    'service_type'     => $serviceType,
                    'body'             => "We are pleased to confirm the terms of our engagement to provide {$service->name} services.\n\nThe scope of our services will be agreed with you and confirmed in writing prior to commencement.",
                    'sort_order'       => $maxSort,
                    'is_active'        => true,
                    'default_included' => false,
                    'is_mandatory'     => false,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        // Not reversible — data changes only
    }
};
