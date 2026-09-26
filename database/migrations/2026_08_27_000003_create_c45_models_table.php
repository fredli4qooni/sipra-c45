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
        Schema::create('c45_models', function (Blueprint $table) {
            $table->id();
            $table->string('nama_model', 150);
            $table->text('deskripsi')->nullable();
            $table->dateTime('train_date');
            $table->string('split_ratio', 20)->default('80:20'); // 80:20, 70:30, dll
            $table->integer('total_training_samples')->default(0);
            $table->integer('total_testing_samples')->default(0);
            $table->string('target_attribute', 50)->default('label_risiko_aktual');
            $table->json('features_used')->nullable(); // Atribut yang dipakai dalam pohon
            
            // Metrik Evaluasi Confusion Matrix
            $table->decimal('accuracy', 5, 2)->default(0.00); // %
            $table->decimal('precision', 5, 2)->default(0.00); // %
            $table->decimal('recall', 5, 2)->default(0.00); // %
            $table->decimal('specificity', 5, 2)->default(0.00); // %
            $table->decimal('f1_score', 5, 2)->default(0.00); // %
            
            $table->json('confusion_matrix_data')->nullable(); // TP, TN, FP, FN matrix per class
            $table->json('entropy_gain_calculations')->nullable(); // Log kalkulasi entropy & gain
            $table->longText('tree_structure_json')->nullable(); // JSON Tree visualizer structure
            
            $table->boolean('is_active')->default(false); // Model aktif untuk prediksi
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('c45_models');
    }
};
