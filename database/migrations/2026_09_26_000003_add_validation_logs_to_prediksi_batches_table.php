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
        Schema::table('prediksi_batches', function (Blueprint $table) {
            $table->integer('skipped_records')->default(0)->after('total_tinggi');
            $table->json('error_logs')->nullable()->after('skipped_records');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prediksi_batches', function (Blueprint $table) {
            $table->dropColumn(['skipped_records', 'error_logs']);
        });
    }
};
