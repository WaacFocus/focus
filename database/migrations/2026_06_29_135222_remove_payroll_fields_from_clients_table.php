<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $cols = ['sa_billed_separately', 'payroll_invoiced_separately', 'payroll_fpa', 'payroll_billing_interval'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('clients', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->boolean('sa_billed_separately')->default(false);
            $table->boolean('payroll_invoiced_separately')->default(false);
            $table->decimal('payroll_fpa', 10, 2)->nullable();
            $table->string('payroll_billing_interval')->nullable();
        });
    }
};
