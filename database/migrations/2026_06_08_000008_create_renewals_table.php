<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('renewals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->string('description');
            $table->date('renewal_date');
            $table->decimal('amount', 10, 2)->nullable();
            $table->enum('status', ['pending', 'renewed', 'cancelled', 'overdue'])->default('pending');
            $table->enum('billing_cycle', ['monthly', 'quarterly', 'annually', 'one_off'])->default('annually');
            $table->date('next_renewal_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('renewals');
    }
};
