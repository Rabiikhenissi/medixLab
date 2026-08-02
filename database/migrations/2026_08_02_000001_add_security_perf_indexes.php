<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->index(['labo_id', 'status'], 'invoices_labo_id_status_index');
            $table->index('created_at', 'invoices_created_at_index');
        });

        Schema::table('samples', function (Blueprint $table) {
            $table->index('status', 'samples_status_index');
        });

        Schema::table('equipment', function (Blueprint $table) {
            $table->index('status', 'equipment_status_index');
        });

        Schema::table('equipment_maintenance', function (Blueprint $table) {
            $table->index(['equipment_id', 'status'], 'equipment_maintenance_equipment_id_status_index');
        });

        Schema::table('doctor_patient_access', function (Blueprint $table) {
            $table->index('expires_at', 'doctor_patient_access_expires_at_index');
        });

        Schema::table('available_exams', function (Blueprint $table) {
            $table->index('is_archive', 'available_exams_is_archive_index');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex('invoices_labo_id_status_index');
            $table->dropIndex('invoices_created_at_index');
        });

        Schema::table('samples', function (Blueprint $table) {
            $table->dropIndex('samples_status_index');
        });

        Schema::table('equipment', function (Blueprint $table) {
            $table->dropIndex('equipment_status_index');
        });

        Schema::table('equipment_maintenance', function (Blueprint $table) {
            $table->dropIndex('equipment_maintenance_equipment_id_status_index');
        });

        Schema::table('doctor_patient_access', function (Blueprint $table) {
            $table->dropIndex('doctor_patient_access_expires_at_index');
        });

        Schema::table('available_exams', function (Blueprint $table) {
            $table->dropIndex('available_exams_is_archive_index');
        });
    }
};
