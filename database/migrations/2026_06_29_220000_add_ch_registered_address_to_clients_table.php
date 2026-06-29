<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('ch_reg_address_line_1', 255)->nullable()->after('ch_sic_codes');
            $table->string('ch_reg_address_line_2', 255)->nullable()->after('ch_reg_address_line_1');
            $table->string('ch_reg_locality', 100)->nullable()->after('ch_reg_address_line_2');
            $table->string('ch_reg_region', 100)->nullable()->after('ch_reg_locality');
            $table->string('ch_reg_postcode', 20)->nullable()->after('ch_reg_region');
            $table->string('ch_reg_country', 100)->nullable()->after('ch_reg_postcode');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'ch_reg_address_line_1', 'ch_reg_address_line_2',
                'ch_reg_locality', 'ch_reg_region',
                'ch_reg_postcode', 'ch_reg_country',
            ]);
        });
    }
};
