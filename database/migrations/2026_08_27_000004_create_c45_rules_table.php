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
        Schema::create('c45_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('model_id')->constrained('c45_models')->cascadeOnDelete();
            $table->string('rule_code', 20); // R1, R2, R3...
            $table->text('rule_text'); // IF IPK = Rendah AND Kehadiran = Kurang THEN Risiko = Tinggi
            $table->json('conditions_json'); // array of ['attribute' => 'kategori_ipk', 'operator' => '=', 'value' => 'Rendah']
            $table->string('decision', 50); // Risiko Rendah, Risiko Sedang, Risiko Tinggi / Berisiko DO
            $table->decimal('confidence', 5, 2)->default(100.00); // %
            $table->integer('support_samples')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('c45_rules');
    }
};
