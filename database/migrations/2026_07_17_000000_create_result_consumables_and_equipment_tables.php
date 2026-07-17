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
        Schema::create('result_consumables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('result_labo_id')->constrained('result_labos')->cascadeOnDelete();
            $table->foreignId('consumable_id')->constrained('consumables')->cascadeOnDelete();
            $table->integer('quantity_used')->default(1);
            $table->boolean('is_archive')->default(false);
            $table->timestamps();
        });

        Schema::create('result_equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('result_labo_id')->constrained('result_labos')->cascadeOnDelete();
            $table->foreignId('equipment_id')->constrained('equipment')->cascadeOnDelete();
            $table->boolean('is_archive')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('result_equipment');
        Schema::dropIfExists('result_consumables');
    }
};
