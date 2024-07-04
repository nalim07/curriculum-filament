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
        Schema::create('tk_attendances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('member_class_school_id')->constrained('member_class_schools')->onDelete('cascade');
            $table->integer('no_school_days')->nullable();
            $table->integer('days_attended')->nullable();
            $table->integer('days_absent')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tk_attendances');
    }
};
