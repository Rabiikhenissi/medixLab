<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doctor_patient_access', function (Blueprint $table) {
            $table->enum('access_status', ['pending', 'granted', 'revoked', 'blocked'])->change();
        });
    }

    public function down(): void
    {
        DB::table('doctor_patient_access')->where('access_status', 'blocked')->update(['access_status' => 'revoked']);
        Schema::table('doctor_patient_access', function (Blueprint $table) {
            $table->enum('access_status', ['pending', 'granted', 'revoked'])->change();
        });
    }
};
