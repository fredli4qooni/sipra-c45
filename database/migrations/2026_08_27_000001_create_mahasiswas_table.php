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
        Schema::create('mahasiswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nim', 30)->unique();
            $table->string('nama', 150);
            $table->integer('angkatan');
            $table->enum('jenis_kelamin', ['L', 'P'])->default('L');
            $table->string('jalur_masuk', 50)->nullable(); // SNBP, SNBT, SPAN-PTKIN, UM-PTKIN, Mandiri
            $table->string('email')->nullable();
            $table->string('no_hp', 25)->nullable();
            $table->text('alamat')->nullable();
            $table->string('tinggal_dengan', 50)->nullable(); // Orang Tua, Kos, Saudara, Asrama
            $table->string('status_mahasiswa', 30)->default('Aktif'); // Aktif, Cuti, Lulus, Drop Out, Non-Aktif
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mahasiswas');
    }
};
