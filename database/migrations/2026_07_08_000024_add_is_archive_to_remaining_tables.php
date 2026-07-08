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
        $tables = [
            'groups',
            'features',
            'actions',
            'group_permissions',
            'labos',
            'working_hours',
            'consumables',
            'stock_movements',
            'equipment',
            'admins',
            'doctors',
            'patients',
            'staff',
            'doctor_patient_access',
            'equipment_maintenance',
            'available_exams',
            'exam_consumables',
            'exam_equipment',
            'exam_groups',
            'exam_group_items',
            'exam_requests',
            'exam_request_items',
            'result_labos',
            'result_labo_details',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'is_archive')) {
                Schema::table($table, function (Blueprint $tableGroup) {
                    $tableGroup->boolean('is_archive')->default(false);
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'groups',
            'features',
            'actions',
            'group_permissions',
            'labos',
            'working_hours',
            'consumables',
            'stock_movements',
            'equipment',
            'admins',
            'doctors',
            'patients',
            'staff',
            'doctor_patient_access',
            'equipment_maintenance',
            'available_exams',
            'exam_consumables',
            'exam_equipment',
            'exam_groups',
            'exam_group_items',
            'exam_requests',
            'exam_request_items',
            'result_labos',
            'result_labo_details',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'is_archive')) {
                Schema::table($table, function (Blueprint $tableGroup) {
                    $tableGroup->dropColumn('is_archive');
                });
            }
        }
    }
};
