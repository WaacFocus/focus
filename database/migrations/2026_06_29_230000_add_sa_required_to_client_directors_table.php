<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_directors', function (Blueprint $table) {
            $table->boolean('sa_required')->default(false)->after('country_of_residence');
        });
    }

    public function down(): void
    {
        Schema::table('client_directors', function (Blueprint $table) {
            $table->dropColumn('sa_required');
        });
    }
};
