<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->string('name', 100);
            $table->string('slug', 50);
            $table->string('color', 50)->default('secondary');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_completion')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $now = now();
        DB::table('job_statuses')->insert([
            ['service_id' => null, 'name' => 'Pending',     'slug' => 'pending',     'color' => 'secondary',    'sort_order' => 1, 'is_completion' => false, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['service_id' => null, 'name' => 'In Progress', 'slug' => 'in_progress', 'color' => 'in-progress',  'sort_order' => 2, 'is_completion' => false, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['service_id' => null, 'name' => 'Completed',   'slug' => 'completed',   'color' => 'success',      'sort_order' => 3, 'is_completion' => true,  'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('job_statuses');
    }
};
