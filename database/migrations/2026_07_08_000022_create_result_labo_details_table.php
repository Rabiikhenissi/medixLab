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
        Schema::create('result_labo_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('result_labo_id')->constrained('result_labos')->cascadeOnDelete();
            $table->string('parameter');
            $table->string('value');
            $table->enum('status', ['normal', 'high', 'low', 'abnormal']);
            $table->string('reference_range')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('result_labo_details');
    }
};
