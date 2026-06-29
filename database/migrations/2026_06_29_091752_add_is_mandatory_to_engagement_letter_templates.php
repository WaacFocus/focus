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
            $table->boolean('is_mandatory')->default(false)->after('default_included');
        });

        // All default_included sections are also mandatory by default
        DB::table('engagement_letter_templates')
            ->where('default_included', true)
            ->update(['is_mandatory' => true]);
    }

    public function down(): void
    {
        Schema::table('engagement_letter_templates', function (Blueprint $table) {
            $table->dropColumn('is_mandatory');
        });
    }
};
