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

        // Get all active service names (lower-case) so we can detect gaps
        $existingServiceTypes = DB::table('services')
            ->pluck('name')
            ->map(fn($n) => strtolower($n))
            ->toArray();

        // Find templates whose service_type has no matching service
        $orphanTypes = DB::table('engagement_letter_templates')
            ->whereNotNull('service_type')
            ->where('service_type', '!=', '')
            ->pluck('service_type')
            ->unique()
            ->reject(fn($type) => in_array(strtolower($type), $existingServiceTypes, true))
            ->values();

        if ($orphanTypes->isEmpty()) {
            return;
        }

        // Load global job statuses to copy for each new service
        $globalStatuses = DB::table('job_statuses')
            ->whereNull('service_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        foreach ($orphanTypes as $serviceType) {
            // Derive a display name: capitalise each word
            $name = ucwords(str_replace(['-', '_'], ' ', $serviceType));

            // Create the service
            $serviceId = DB::table('services')->insertGetId([
                'name'       => $name,
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Seed service-specific job statuses (copy from globals)
            foreach ($globalStatuses as $gs) {
                DB::table('job_statuses')->insert([
                    'service_id'    => $serviceId,
                    'name'          => $gs->name,
                    'slug'          => $gs->slug,
                    'color'         => $gs->color,
                    'sort_order'    => $gs->sort_order,
                    'is_completion' => $gs->is_completion,
                    'is_active'     => true,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }

            // Update the template title to match the derived name (service_type stays as-is)
            DB::table('engagement_letter_templates')
                ->where('service_type', $serviceType)
                ->update(['title' => $name, 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        // Not reversible — data changes only
    }
};
