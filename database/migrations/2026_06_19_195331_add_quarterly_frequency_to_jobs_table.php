<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('practice_jobs', function (Blueprint $table) {
            $table->enum('frequency', ['weekly', 'monthly', 'quarterly', 'yearly', 'one-off'])
                  ->default('monthly')
                  ->change();
        });
    }

    public function down(): void
    {
        Schema::table('practice_jobs', function (Blueprint $table) {
            $table->enum('frequency', ['weekly', 'monthly', 'yearly', 'one-off'])
                  ->default('monthly')
                  ->change();
        });
    }
};
