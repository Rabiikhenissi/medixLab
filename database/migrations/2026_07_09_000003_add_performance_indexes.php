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
        // Add indexes to improve query performance
        Schema::table('notifications', function (Blueprint $table) {
            $table->index('user_id');
            $table->index(['user_id', 'is_read']);
            $table->index(['user_id', 'is_archive']);
            $table->index('created_at');
        });

        Schema::table('doctor_patient_access', function (Blueprint $table) {
            $table->index('doctor_id');
            $table->index('patient_id');
            $table->index(['doctor_id', 'patient_id']);
            $table->index('access_status');
        });

        Schema::table('exam_requests', function (Blueprint $table) {
            $table->index('doctor_id');
            $table->index('patient_id');
            $table->index('status');
            $table->index('created_at');
        });

        Schema::table('exam_request_items', function (Blueprint $table) {
            $table->index('exam_request_id');
            $table->index('exam_id');
        });

        Schema::table('exam_groups', function (Blueprint $table) {
            $table->index('doctor_id');
            $table->index('is_archive');
        });

        Schema::table('exam_group_items', function (Blueprint $table) {
            $table->index('exam_group_id');
            $table->index('exam_id');
        });

        Schema::table('exams', function (Blueprint $table) {
            $table->index('is_archive');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['user_id', 'is_read']);
            $table->dropIndex(['user_id', 'is_archive']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('doctor_patient_access', function (Blueprint $table) {
            $table->dropIndex(['doctor_id']);
            $table->dropIndex(['patient_id']);
            $table->dropIndex(['doctor_id', 'patient_id']);
            $table->dropIndex(['access_status']);
        });

        Schema::table('exam_requests', function (Blueprint $table) {
            $table->dropIndex(['doctor_id']);
            $table->dropIndex(['patient_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('exam_request_items', function (Blueprint $table) {
            $table->dropIndex(['exam_request_id']);
            $table->dropIndex(['exam_id']);
        });

        Schema::table('exam_groups', function (Blueprint $table) {
            $table->dropIndex(['doctor_id']);
            $table->dropIndex(['is_archive']);
        });

        Schema::table('exam_group_items', function (Blueprint $table) {
            $table->dropIndex(['exam_group_id']);
            $table->dropIndex(['exam_id']);
        });

        Schema::table('exams', function (Blueprint $table) {
            $table->dropIndex(['is_archive']);
        });
    }
};
