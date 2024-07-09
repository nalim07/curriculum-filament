<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_sumatif_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_data_id')->constrained('learning_data')->onDelete('cascade');
            $table->foreignId('semester_id')->constrained('semesters')->onDelete('cascade');
            $table->foreignId('term_id')->constrained('terms')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('plan_sumatif_value_techniques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_sumatif_value_id')->constrained('plan_sumatif_values')->onDelete('cascade');
            $table->string('code');
            $table->enum('technique', ['1', '2']);
            $table->integer('weighting');
            $table->timestamps();

            // Teknik Penilaian
            // 1 = Tes Tulis
            // 2 = Tes Lisan
            // 3 = Penugasan
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_sumatif_value_techniques');
        Schema::dropIfExists('plan_sumatif_values');
    }
};
