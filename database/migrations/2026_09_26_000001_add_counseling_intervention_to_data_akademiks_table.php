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
        Schema::table('data_akademiks', function (Blueprint $table) {
            $table->string('status_intervensi', 50)->default('Belum Ditindaklanjuti')->after('keterangan');
            $table->string('tindakan_intervensi', 150)->nullable()->after('status_intervensi');
            $table->text('catatan_intervensi')->nullable()->after('tindakan_intervensi');
            $table->dateTime('tanggal_intervensi')->nullable()->after('catatan_intervensi');
            $table->foreignId('dosen_pa_id')->nullable()->constrained('users')->nullOnDelete()->after('tanggal_intervensi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_akademiks', function (Blueprint $table) {
            $table->dropForeign(['dosen_pa_id']);
            $table->dropColumn([
                'status_intervensi',
                'tindakan_intervensi',
                'catatan_intervensi',
                'tanggal_intervensi',
                'dosen_pa_id',
            ]);
        });
    }
};
