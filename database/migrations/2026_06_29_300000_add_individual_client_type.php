<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! DB::table('client_types')->where('name', 'Individual')->exists()) {
            DB::table('client_types')->insert([
                'name'       => 'Individual',
                'sort_order' => 4,
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('client_types')->where('name', 'Individual')->delete();
    }
};
