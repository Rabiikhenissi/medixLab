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
            // Make labo_id nullable to allow doctors to create requests without selecting a lab
            $table->foreignId('labo_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_requests', function (Blueprint $table) {
            $table->foreignId('labo_id')->nullable(false)->change();
        });
    }
};
