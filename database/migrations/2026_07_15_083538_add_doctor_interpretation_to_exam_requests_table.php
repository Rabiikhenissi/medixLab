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
        Schema::table('exam_requests', function (Blueprint $table) {
            $table->text('doctor_interpretation')->nullable()->after('clinical_notes');
            $table->boolean('approved_by_doctor')->default(false)->after('doctor_interpretation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_requests', function (Blueprint $table) {
            $table->dropColumn(['doctor_interpretation', 'approved_by_doctor']);
        });
    }
};
