<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('engagement_letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('renewal_id')->nullable()->constrained('renewals')->nullOnDelete();
            $table->foreignId('sent_by')->nullable()->references('id')->on('users')->nullOnDelete();
            $table->string('subject');
            $table->json('sections');
            $table->longText('composed_html')->nullable();
            $table->string('token', 64)->unique()->nullable();
            $table->enum('status', ['draft', 'sent', 'signed'])->default('draft');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->string('signed_name')->nullable();
            $table->string('signed_ip', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('engagement_letters');
    }
};
