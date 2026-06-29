<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('ch_status', 100)->nullable()->after('company_number');
            $table->date('ch_incorporated_on')->nullable()->after('ch_status');
            $table->string('ch_jurisdiction', 100)->nullable()->after('ch_incorporated_on');
            $table->string('ch_sic_codes', 255)->nullable()->after('ch_jurisdiction');
            $table->date('ch_accounts_year_end')->nullable()->after('ch_sic_codes');
            $table->date('ch_accounts_next_due')->nullable()->after('ch_accounts_year_end');
            $table->date('ch_confirmation_statement_next_due')->nullable()->after('ch_accounts_next_due');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'ch_status', 'ch_incorporated_on', 'ch_jurisdiction',
                'ch_sic_codes', 'ch_accounts_year_end',
                'ch_accounts_next_due', 'ch_confirmation_statement_next_due',
            ]);
        });
    }
};
