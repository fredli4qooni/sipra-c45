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
        Schema::table('c45_models', function (Blueprint $table) {
            $table->unsignedInteger('random_seed')->default(42)->after('split_ratio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('c45_models', function (Blueprint $table) {
            $table->dropColumn('random_seed');
        });
    }
};
