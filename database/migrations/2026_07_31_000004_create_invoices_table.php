<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 50)->unique();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('labo_id')->constrained()->onDelete('cascade');
            $table->foreignId('exam_request_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('status', ['pending', 'paid', 'partially_paid', 'cancelled'])->default('pending');
            $table->decimal('total_amount', 12, 3)->default(0);
            $table->decimal('cnam_amount', 12, 3)->default(0)->comment('Covered by CNAM');
            $table->decimal('patient_amount', 12, 3)->default(0)->comment('Patient out-of-pocket');
            $table->decimal('paid_amount', 12, 3)->default(0);
            $table->string('payment_method', 50)->nullable();
            $table->string('payment_reference', 100)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('exam_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('exam_request_item_id')->nullable()->constrained('exam_request_items')->onDelete('set null');
            $table->string('description', 255);
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 12, 3)->default(0);
            $table->decimal('total', 12, 3)->default(0);
            $table->string('cnam_code', 20)->nullable();
            $table->decimal('valeur_b', 10, 3)->nullable();
            $table->decimal('cnam_coverage', 12, 3)->default(0);
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 12, 3);
            $table->string('payment_method', 50);
            $table->string('transaction_id', 100)->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('payment_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
    }
};
