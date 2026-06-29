<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('engagement_letter_templates', function (Blueprint $table) {
            $table->boolean('default_included')->default(false)->after('is_active');
        });

        // Pre-tick the five standard sections
        DB::table('engagement_letter_templates')
            ->whereIn('title', [
                'Introduction',
                'Our Responsibilities',
                'Client Responsibilities',
                'Confidentiality & Data Protection',
                'Acceptance of Terms',
            ])
            ->update(['default_included' => true]);
    }

    public function down(): void
    {
        Schema::table('engagement_letter_templates', function (Blueprint $table) {
            $table->dropColumn('default_included');
        });
    }
};
