<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Change status from enum to varchar for MySQL; SQLite already stores as text
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE practice_jobs MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'pending'");
        }

        Schema::table('practice_jobs', function (Blueprint $table) {
            $table->foreignId('service_id')->nullable()->after('client_id')->constrained('services')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('practice_jobs', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropColumn('service_id');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE practice_jobs MODIFY COLUMN status ENUM('pending','in_progress','completed') NOT NULL DEFAULT 'pending'");
        }
    }
};
