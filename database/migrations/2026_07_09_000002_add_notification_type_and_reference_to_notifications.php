<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Add fields to link notifications to related models
            $table->string('notification_type')->nullable(); // 'access_request', 'exam_request', etc.
            $table->unsignedBigInteger('reference_id')->nullable(); // ID of the related model (access_id, exam_request_id, etc.)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn(['notification_type', 'reference_id']);
        });
    }
};
