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
        Schema::create('data_akademiks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->cascadeOnDelete();
            $table->integer('semester'); // 1 sampai 8
            $table->string('tahun_akademik', 30)->nullable(); // e.g. 2021/2022 Ganjil
            $table->decimal('ips', 4, 2)->default(0.00); // Indeks Prestasi Semester
            $table->decimal('ipk', 4, 2)->default(0.00); // Indeks Prestasi Kumulatif
            $table->integer('sks_semester')->default(0); // SKS semester ini
            $table->integer('sks_total')->default(0); // SKS kumulatif
            $table->integer('sks_tidak_lulus')->default(0); // SKS nilai D/E/F
            $table->decimal('persentase_kehadiran', 5, 2)->default(0.00); // % Kehadiran kuliah
            $table->integer('jumlah_kehadiran')->nullable();
            $table->integer('total_pertemuan')->nullable();
            $table->boolean('status_cuti')->default(false);
            
            // Kategori diskretisasi untuk algoritma C4.5
            $table->string('kategori_ipk', 30)->nullable(); // Rendah (<2.75), Cukup (2.75-3.25), Tinggi (>3.25)
            $table->string('kategori_ips', 30)->nullable(); // Rendah (<2.75), Cukup (2.75-3.25), Tinggi (>3.25)
            $table->string('kategori_sks', 30)->nullable(); // Kurang (<18), Cukup (18-21), Sangat Baik (>21)
            $table->string('kategori_kehadiran', 30)->nullable(); // Kurang (<75%), Cukup (75%-85%), Baik (>85%)
            
            // Label status risiko aktual (bila ada untuk training data)
            $table->string('label_risiko_aktual', 30)->nullable(); // Rendah, Sedang, Tinggi
            $table->string('label_do_aktual', 30)->nullable(); // Tidak Berisiko, Berisiko
            
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_akademiks');
    }
};
