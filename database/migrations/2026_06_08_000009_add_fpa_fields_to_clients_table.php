<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('client_code')->nullable()->after('id');
            $table->string('team_cam')->nullable()->after('status');
            $table->date('fpa_year_end')->nullable()->after('team_cam');
            $table->decimal('fpa_amount', 10, 2)->nullable()->after('fpa_year_end');
            $table->string('billing_interval')->nullable()->after('fpa_amount');
            $table->boolean('sa_billed_separately')->default(false)->after('billing_interval');
            $table->boolean('payroll_invoiced_separately')->default(false)->after('sa_billed_separately');
            $table->decimal('payroll_fpa', 10, 2)->nullable()->after('payroll_invoiced_separately');
            $table->string('payroll_billing_interval')->nullable()->after('payroll_fpa');
            $table->string('payment_method')->nullable()->after('payroll_billing_interval');
            $table->string('tcp_company')->nullable()->after('payment_method');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'client_code', 'team_cam', 'fpa_year_end', 'fpa_amount',
                'billing_interval', 'sa_billed_separately', 'payroll_invoiced_separately',
                'payroll_fpa', 'payroll_billing_interval', 'payment_method', 'tcp_company',
            ]);
        });
    }
};
