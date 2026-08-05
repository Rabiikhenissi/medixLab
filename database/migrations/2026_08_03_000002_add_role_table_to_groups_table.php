<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add a role_table hint to groups so each role knows which
     * profile table (admins/doctors/patients/staff) its users belong to.
     */
    public function up(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->string('role_table')->nullable()->after('code');
        });

        // Backfill existing groups based on their code
        $mapping = [
            'admin' => 'admin',
            'doctor' => 'doctor',
            'patient' => 'patient',
            'center' => 'staff',
        ];

        foreach ($mapping as $code => $roleTable) {
            DB::table('groups')->where('code', $code)->update(['role_table' => $roleTable]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn('role_table');
        });
    }
};
