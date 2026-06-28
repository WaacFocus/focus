<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add new columns only if they don't already exist (migration may have partially run)
        Schema::table('renewals', function (Blueprint $table) {
            if (!Schema::hasColumn('renewals', 'completed_date')) {
                $table->date('completed_date')->nullable()->after('description');
            }
            if (!Schema::hasColumn('renewals', 'due_date')) {
                $table->date('due_date')->nullable()->after('completed_date');
            }
        });

        // Copy renewal_date → due_date where due_date not yet set
        if (Schema::hasColumn('renewals', 'renewal_date')) {
            DB::statement('UPDATE renewals SET due_date = renewal_date WHERE due_date IS NULL');
        }

        // Drop old columns (skip FK drop if service_id already gone)
        Schema::table('renewals', function (Blueprint $table) {
            $toDrop = [];
            foreach (['service_id', 'amount', 'billing_cycle', 'next_renewal_date', 'renewal_date'] as $col) {
                if (Schema::hasColumn('renewals', $col)) {
                    $toDrop[] = $col;
                }
            }
            if (in_array('service_id', $toDrop)) {
                // Use raw statement to drop the FK with its actual name
                DB::statement('ALTER TABLE renewals DROP FOREIGN KEY renewals_ibfk_1');
                DB::statement('ALTER TABLE renewals DROP INDEX service_id');
            }
            if (!empty($toDrop)) {
                $table->dropColumn($toDrop);
            }
        });
    }

    public function down(): void
    {
        Schema::table('renewals', function (Blueprint $table) {
            $table->date('renewal_date')->nullable()->after('description');
            $table->decimal('amount', 10, 2)->nullable()->after('renewal_date');
            $table->string('billing_cycle', 20)->default('annually')->after('amount');
            $table->date('next_renewal_date')->nullable();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
        });

        DB::statement('UPDATE renewals SET renewal_date = due_date');

        Schema::table('renewals', function (Blueprint $table) {
            $table->dropColumn(['completed_date', 'due_date']);
        });
    }
};
