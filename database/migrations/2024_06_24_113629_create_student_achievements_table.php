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
        Schema::create('student_achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semester_id')->constrained('semesters')->onDelete('cascade');
            $table->foreignId('class_school_id')->constrained('class_schools')->onDelete('cascade');
            $table->foreignId('member_class_school_id')->constrained('member_class_schools')->onDelete('cascade');
            $table->string('name', 100);
            $table->enum('type_of_achievement', ['1', '2']);
            $table->enum('level_achievement', ['1', '2', '3', '4', '5', '6']);
            $table->string('description', 200);
            $table->timestamps();

            // Jenis Prestasi 
            // 1 = Akademik 
            // 2 = Non Akademik

            // Tingkat Prestasi
            // 1 = Internations
            // 2 = National
            // 3 = Province
            // 4 = City
            // 5 = District
            // 6 = Inter School
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_achievements');
    }
};
