<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_directors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('role', 100)->default('director');
            $table->date('appointed_on')->nullable();
            $table->date('resigned_on')->nullable();
            $table->unsignedTinyInteger('dob_month')->nullable();
            $table->unsignedSmallInteger('dob_year')->nullable();
            $table->string('nationality', 100)->nullable();
            $table->string('occupation', 255)->nullable();
            $table->string('country_of_residence', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_directors');
    }
};
