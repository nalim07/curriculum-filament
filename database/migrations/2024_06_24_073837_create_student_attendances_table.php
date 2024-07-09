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
        Schema::create('student_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semester_id')->constrained('semesters')->onDelete('cascade');
            $table->foreignId('class_school_id')->nullable()->constrained('class_schools')->onDelete('cascade');
            $table->foreignId('member_class_school_id')->constrained('member_class_schools')->onDelete('cascade');
            $table->integer('sick')->nullable();
            $table->integer('permission')->nullable();
            $table->integer('without_explanation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_attandances');
    }
};
