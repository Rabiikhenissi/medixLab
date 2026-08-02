<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds composite indexes that match the most common WHERE / ORDER BY /
     * GROUP BY combinations used by the dashboards, listings and services.
     * Composite indexes let MySQL serve multi-column filters and sorted lists
     * without a filesort or full scan.
     */
    public function up(): void
    {
        Schema::table('exam_requests', function (Blueprint $table) {
            $table->index(['labo_id', 'status', 'created_at'], 'exam_requests_labo_status_created_index');
            $table->index(['doctor_id', 'status'], 'exam_requests_doctor_status_index');
            $table->index(['patient_id', 'status'], 'exam_requests_patient_status_index');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->index(['labo_id', 'status', 'created_at'], 'invoices_labo_status_created_index');
            $table->index(['patient_id', 'created_at'], 'invoices_patient_created_index');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['user_id', 'is_archive', 'is_read'], 'notifications_user_archive_read_index');
        });

        Schema::table('samples', function (Blueprint $table) {
            $table->index(['labo_id', 'status'], 'samples_labo_status_index');
            $table->index(['labo_id', 'created_at'], 'samples_labo_created_index');
        });

        Schema::table('equipment', function (Blueprint $table) {
            $table->index(['labo_id', 'is_archive'], 'equipment_labo_archive_index');
            $table->index(['labo_id', 'name'], 'equipment_labo_name_index');
        });

        Schema::table('consumables', function (Blueprint $table) {
            $table->index(['labo_id', 'is_archive'], 'consumables_labo_archive_index');
            $table->index(['labo_id', 'name'], 'consumables_labo_name_index');
        });

        Schema::table('equipment_maintenance', function (Blueprint $table) {
            $table->index(['equipment_id', 'status', 'created_at'], 'equipment_maintenance_equip_status_created_index');
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->index(['consumable_id', 'created_at'], 'stock_movements_consumable_created_index');
        });

        Schema::table('machine_configurations', function (Blueprint $table) {
            $table->index(['labo_id', 'is_archive', 'enabled'], 'machine_configs_labo_archive_enabled_index');
        });

        Schema::table('chat_messages', function (Blueprint $table) {
            $table->index(['receiver_id', 'sender_id'], 'chat_messages_receiver_sender_index');
        });

        Schema::table('available_exams', function (Blueprint $table) {
            $table->index(['labo_id', 'exam_id', 'is_archive'], 'available_exams_labo_exam_archive_index');
            $table->index(['labo_id', 'is_archive', 'created_at'], 'available_exams_labo_archive_created_index');
        });

        Schema::table('cnam_affiliations', function (Blueprint $table) {
            $table->index(['patient_id', 'is_active'], 'cnam_affiliations_patient_active_index');
        });

        Schema::table('exam_groups', function (Blueprint $table) {
            $table->index(['doctor_id', 'is_archive', 'created_at'], 'exam_groups_doctor_archive_created_index');
        });

        Schema::table('result_labo_details', function (Blueprint $table) {
            $table->index(['result_labo_id', 'status'], 'result_labo_details_result_status_index');
        });

        Schema::table('result_consumables', function (Blueprint $table) {
            $table->index(['result_labo_id', 'consumable_id'], 'result_consumables_result_consumable_index');
        });

        Schema::table('result_equipment', function (Blueprint $table) {
            $table->index(['result_labo_id', 'equipment_id'], 'result_equipment_result_equipment_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_requests', function (Blueprint $table) {
            $table->dropIndex('exam_requests_labo_status_created_index');
            $table->dropIndex('exam_requests_doctor_status_index');
            $table->dropIndex('exam_requests_patient_status_index');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex('invoices_labo_status_created_index');
            $table->dropIndex('invoices_patient_created_index');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('notifications_user_archive_read_index');
        });

        Schema::table('samples', function (Blueprint $table) {
            $table->dropIndex('samples_labo_status_index');
            $table->dropIndex('samples_labo_created_index');
        });

        Schema::table('equipment', function (Blueprint $table) {
            $table->dropIndex('equipment_labo_archive_index');
            $table->dropIndex('equipment_labo_name_index');
        });

        Schema::table('consumables', function (Blueprint $table) {
            $table->dropIndex('consumables_labo_archive_index');
            $table->dropIndex('consumables_labo_name_index');
        });

        Schema::table('equipment_maintenance', function (Blueprint $table) {
            $table->dropIndex('equipment_maintenance_equip_status_created_index');
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropIndex('stock_movements_consumable_created_index');
        });

        Schema::table('machine_configurations', function (Blueprint $table) {
            $table->dropIndex('machine_configs_labo_archive_enabled_index');
        });

        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropIndex('chat_messages_receiver_sender_index');
        });

        Schema::table('available_exams', function (Blueprint $table) {
            $table->dropIndex('available_exams_labo_exam_archive_index');
            $table->dropIndex('available_exams_labo_archive_created_index');
        });

        Schema::table('cnam_affiliations', function (Blueprint $table) {
            $table->dropIndex('cnam_affiliations_patient_active_index');
        });

        Schema::table('exam_groups', function (Blueprint $table) {
            $table->dropIndex('exam_groups_doctor_archive_created_index');
        });

        Schema::table('result_labo_details', function (Blueprint $table) {
            $table->dropIndex('result_labo_details_result_status_index');
        });

        Schema::table('result_consumables', function (Blueprint $table) {
            $table->dropIndex('result_consumables_result_consumable_index');
        });

        Schema::table('result_equipment', function (Blueprint $table) {
            $table->dropIndex('result_equipment_result_equipment_index');
        });
    }
};
