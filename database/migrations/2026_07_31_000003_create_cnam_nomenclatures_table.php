<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cnam_nomenclatures', function (Blueprint $table) {
            $table->id();
            $table->string('code_cnam', 20)->unique();
            $table->string('exam_name', 255);
            $table->decimal('valeur_b', 10, 3)->default(0);
            $table->decimal('taux', 5, 2)->default(0);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('cnam_rates', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('label', 255);
            $table->decimal('taux', 5, 2)->comment('Coverage percentage');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('cnam_affiliations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->string('cnam_number', 50)->unique();
            $table->string('affiliation_number', 50)->nullable();
            $table->foreignId('cnam_rate_id')->constrained('cnam_rates')->onDelete('restrict');
            $table->date('valid_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cnam_affiliations');
        Schema::dropIfExists('cnam_rates');
        Schema::dropIfExists('cnam_nomenclatures');
    }
};
