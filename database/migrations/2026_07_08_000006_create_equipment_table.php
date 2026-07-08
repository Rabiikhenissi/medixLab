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
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('labo_id')->constrained('labos')->cascadeOnDelete();
            $table->string('name');
            $table->string('type')->nullable();
            $table->string('serial_number')->nullable();
            $table->enum('status', ['active', 'inactive', 'maintenance', 'retired']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
