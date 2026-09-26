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
        Schema::create('prediksis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->nullable()->constrained('mahasiswas')->nullOnDelete();
            $table->foreignId('model_id')->nullable()->constrained('c45_models')->nullOnDelete();
            $table->foreignId('rule_id')->nullable()->constrained('c45_rules')->nullOnDelete();
            $table->foreignId('batch_id')->nullable()->constrained('prediksi_batches')->nullOnDelete();
            
            $table->string('nim', 30)->nullable();
            $table->string('nama_mahasiswa', 150);
            $table->integer('semester')->nullable();
            $table->json('input_params_json'); // IPK, IPS, SKS, Kehadiran, dll
            
            // Output Prediksi
            $table->string('hasil_klasifikasi', 50); // Risiko Rendah, Risiko Sedang, Risiko Tinggi
            $table->string('status_do', 50)->default('Tidak Berisiko'); // Berisiko DO / Tidak Berisiko DO
            $table->decimal('confidence_score', 5, 2)->default(100.00); // %
            $table->text('rekomendasi_akademik')->nullable(); // Tindakan rekomendasi PA/Prodi
            
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prediksis');
    }
};
