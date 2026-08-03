<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('result_labo_details', function (Blueprint $table) {
            $table->enum('status', ['normal', 'high', 'low', 'abnormal', 'critical'])->change();
        });
    }

    public function down(): void
    {
        Schema::table('result_labo_details', function (Blueprint $table) {
            $table->enum('status', ['normal', 'high', 'low', 'abnormal'])->change();
        });
    }
};
