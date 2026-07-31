<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('samples', function (Blueprint $table) {
            $table->id();
            $table->string('sample_code', 50)->unique();
            $table->foreignId('exam_request_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('labo_id')->constrained()->onDelete('cascade');
            $table->foreignId('collected_by')->nullable()->constrained('staff')->onDelete('set null');
            $table->string('material_type', 50)->nullable();
            $table->enum('status', ['pending', 'collected', 'in_transit', 'received', 'processing', 'completed', 'rejected'])->default('pending');
            $table->string('rejection_reason')->nullable();
            $table->string('storage_location', 100)->nullable();
            $table->date('collection_date')->nullable();
            $table->time('collection_time')->nullable();
            $table->date('expiry_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('sample_barcode_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sample_id')->constrained()->onDelete('cascade');
            $table->enum('action', ['created', 'collected', 'scanned', 'transferred', 'received', 'processing', 'completed', 'rejected']);
            $table->foreignId('staff_id')->nullable()->constrained()->onDelete('set null');
            $table->string('location', 100)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::table('consumables', function (Blueprint $table) {
            $table->date('expiry_date')->nullable()->after('min_quantity');
            $table->string('batch_number', 100)->nullable()->after('expiry_date');
            $table->string('barcode', 100)->nullable()->after('batch_number');
        });
    }

    public function down(): void
    {
        Schema::table('consumables', function (Blueprint $table) {
            $table->dropColumn(['expiry_date', 'batch_number', 'barcode']);
        });
        Schema::dropIfExists('sample_barcode_logs');
        Schema::dropIfExists('samples');
    }
};
