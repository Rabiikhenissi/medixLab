<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('machine_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('labo_id')->constrained('labos')->cascadeOnDelete();
            $table->string('name');
            $table->string('host')->default('127.0.0.1');
            $table->integer('port')->default(5000);
            $table->string('protocol', 20)->default('hl7_mllp');
            $table->integer('mllp_port')->nullable();
            $table->string('api_key')->nullable();
            $table->integer('timeout')->default(15);
            $table->boolean('enabled')->default(true);
            $table->boolean('is_archive')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('machine_configurations');
    }
};
