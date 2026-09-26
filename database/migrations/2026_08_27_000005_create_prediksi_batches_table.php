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
        Schema::create('prediksi_batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_code', 50)->unique();
            $table->string('file_name', 200)->nullable();
            $table->foreignId('model_id')->nullable()->constrained('c45_models')->nullOnDelete();
            $table->integer('total_records')->default(0);
            $table->integer('total_rendah')->default(0);
            $table->integer('total_sedang')->default(0);
            $table->integer('total_tinggi')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prediksi_batches');
    }
};
