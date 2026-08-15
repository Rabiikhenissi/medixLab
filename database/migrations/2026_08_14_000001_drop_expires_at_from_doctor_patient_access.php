<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Doctor access is now permanent until the patient blocks it, so the
     * expiry timestamp and its index are no longer needed.
     */
    public function up(): void
    {
        Schema::table('doctor_patient_access', function (Blueprint $table) {
            $table->dropIndex('doctor_patient_access_expires_at_index');
            $table->dropColumn('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doctor_patient_access', function (Blueprint $table) {
            $table->timestamp('expires_at')->nullable()->after('access_status');
            $table->index('expires_at', 'doctor_patient_access_expires_at_index');
        });
    }
};
