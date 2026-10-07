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
        Schema::create('kategori_risikos', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 50)->unique(); // tinggi, sedang, rendah
            $table->string('nama_risiko', 100);  // Risiko Tinggi, Risiko Sedang, Risiko Rendah
            $table->string('label_badge', 100);  // Perhatian Khusus, Waspada, Aman
            $table->string('warna', 30)->default('rose'); // rose, amber, brand
            $table->string('icon', 50)->default('alert-triangle'); // alert-triangle, alert-circle, check-circle-2
            $table->text('deskripsi_singkat')->nullable();
            $table->text('pesan_peringatan');
            $table->text('rekomendasi_studi');
            $table->json('panduan_konsultasi_pa')->nullable();
            $table->text('template_wa')->nullable();
            $table->integer('urutan')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori_risikos');
    }
};
