<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('contact_title', 20)->nullable()->after('company_name');
            $table->string('contact_first_name', 100)->nullable()->after('contact_title');
            $table->string('contact_last_name', 100)->nullable()->after('contact_first_name');
        });

        // Migrate existing contact_name: first word → first_name, rest → last_name
        DB::table('clients')->whereNotNull('contact_name')->where('contact_name', '!=', '')->get()
            ->each(function ($client) {
                $parts     = preg_split('/\s+/', trim($client->contact_name), 2);
                $firstName = $parts[0] ?? null;
                $lastName  = $parts[1] ?? null;
                DB::table('clients')->where('id', $client->id)->update([
                    'contact_first_name' => $firstName,
                    'contact_last_name'  => $lastName,
                ]);
            });

        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('contact_name');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('contact_name', 255)->nullable()->after('company_name');
        });

        DB::table('clients')->get()->each(function ($client) {
            $parts = array_filter([$client->contact_first_name, $client->contact_last_name]);
            DB::table('clients')->where('id', $client->id)->update([
                'contact_name' => $parts ? implode(' ', $parts) : null,
            ]);
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['contact_title', 'contact_first_name', 'contact_last_name']);
        });
    }
};
