<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('engagement_letters', function (Blueprint $table) {
            $table->string('transaction_id', 36)->nullable()->unique()->after('signed_ip');
            $table->longText('signature_image')->nullable()->after('transaction_id');
            $table->string('signature_type', 10)->nullable()->after('signature_image');
            $table->text('signed_user_agent')->nullable()->after('signature_type');
        });
    }

    public function down(): void
    {
        Schema::table('engagement_letters', function (Blueprint $table) {
            $table->dropColumn(['transaction_id', 'signature_image', 'signature_type', 'signed_user_agent']);
        });
    }
};
