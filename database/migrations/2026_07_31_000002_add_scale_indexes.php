<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_requests', function (Blueprint $table) {
            $table->index(['labo_id', 'status']);
            $table->index(['doctor_id', 'created_at']);
            $table->index(['patient_id', 'created_at']);
            $table->index(['labo_id', 'created_at']);
            $table->index('approved_by_doctor');
        });

        Schema::table('exam_request_items', function (Blueprint $table) {
            $table->index(['exam_request_id', 'exam_id']);
        });

        Schema::table('exams', function (Blueprint $table) {
            $table->index('code');
            $table->index('category');
            if (Schema::getConnection()->getDriverName() === 'mysql') {
                $table->fullText('name', 'exams_name_fulltext');
                $table->fullText('description', 'exams_description_fulltext');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('first_name');
            $table->index('last_name');
            $table->index('email');
            if (Schema::getConnection()->getDriverName() === 'mysql') {
                $table->fullText('first_name', 'users_first_name_fulltext');
                $table->fullText('last_name', 'users_last_name_fulltext');
            }
        });

        Schema::table('labos', function (Blueprint $table) {
            $table->index('city');
            $table->index('is_archive');
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->index('patient_code');
        });

        Schema::table('chat_messages', function (Blueprint $table) {
            $table->index(['sender_id', 'receiver_id']);
            $table->index(['receiver_id', 'is_read']);
            $table->index('created_at');
        });

        Schema::table('available_exams', function (Blueprint $table) {
            $table->index(['labo_id', 'exam_id']);
            $table->index(['labo_id', 'is_active']);
        });

        Schema::table('result_labos', function (Blueprint $table) {
            $table->index('exam_request_item_id');
        });

        Schema::table('result_labo_details', function (Blueprint $table) {
            $table->index('result_labo_id');
            $table->index('parameter');
        });
    }

    public function down(): void
    {
        Schema::table('exam_requests', function (Blueprint $table) {
            $table->dropIndex(['labo_id', 'status']);
            $table->dropIndex(['doctor_id', 'created_at']);
            $table->dropIndex(['patient_id', 'created_at']);
            $table->dropIndex(['labo_id', 'created_at']);
            $table->dropIndex(['approved_by_doctor']);
        });
        Schema::table('exam_request_items', function (Blueprint $table) {
            $table->dropIndex(['exam_request_id', 'exam_id']);
        });
        Schema::table('exams', function (Blueprint $table) {
            $table->dropIndex(['code']);
            $table->dropIndex(['category']);
            if (Schema::getConnection()->getDriverName() === 'mysql') {
                $table->dropIndex('exams_name_fulltext');
                $table->dropIndex('exams_description_fulltext');
            }
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['first_name']);
            $table->dropIndex(['last_name']);
            $table->dropIndex(['email']);
            if (Schema::getConnection()->getDriverName() === 'mysql') {
                $table->dropIndex('users_first_name_fulltext');
                $table->dropIndex('users_last_name_fulltext');
            }
        });
        Schema::table('labos', function (Blueprint $table) {
            $table->dropIndex(['city']);
            $table->dropIndex(['is_archive']);
        });
        Schema::table('patients', function (Blueprint $table) {
            $table->dropIndex(['patient_code']);
        });
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropIndex(['sender_id', 'receiver_id']);
            $table->dropIndex(['receiver_id', 'is_read']);
            $table->dropIndex(['created_at']);
        });
        Schema::table('available_exams', function (Blueprint $table) {
            $table->dropIndex(['labo_id', 'exam_id']);
            $table->dropIndex(['labo_id', 'is_active']);
        });
        Schema::table('result_labos', function (Blueprint $table) {
            $table->dropIndex(['exam_request_item_id']);
        });
        Schema::table('result_labo_details', function (Blueprint $table) {
            $table->dropIndex(['result_labo_id']);
            $table->dropIndex(['parameter']);
        });
    }
};
