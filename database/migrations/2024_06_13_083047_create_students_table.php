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
        Schema::create('students', function (Blueprint $table) {
            // student information
            $table->id();

            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');

            $table->string('fullname', 100);
            $table->string('username', 100);
            $table->string('email');
            $table->string('nis', 10);
            $table->string('nisn', 10)->nullable();
            $table->string('nik', 16)->nullable();

            $table->enum('registration_type', ['1', '2']);
            $table->string('entry_year')->nullable();
            $table->string('entry_semester')->nullable();
            $table->string('entry_class')->nullable();
            $table->foreignId('class_school_id')->nullable()->constrained('class_schools')->onDelete('cascade');
            $table->foreignId('level_id')->nullable()->constrained('levels')->onDelete('cascade');
            $table->foreignId('line_id')->nullable()->constrained('lines')->onDelete('cascade');

            $table->enum('gender', ['1', '2'])->nullable();
            $table->enum('blood_type', ['A', 'B', 'AB', 'O'])->nullable();
            $table->enum('religion', ['1', '2', '3', '4', '5', '6', '7'])->nullable();
            $table->string('place_of_birth', 50)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('anak_ke', 2)->nullable();
            $table->string('number_of_sibling', 2)->nullable();
            $table->string('citizen')->nullable();

            // domicile information
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->unsignedInteger('postal_code')->nullable();
            $table->unsignedInteger('distance_home_to_school')->nullable();
            $table->string('email_parent')->nullable();
            $table->string('phone_number', 13)->nullable();
            $table->enum('living_together', ['1', '2'])->nullable(); // 1 parents, 2 other
            $table->string('transportation')->nullable();

            //// parent information
            // parent information father
            $table->string('nik_father', 16)->nullable();
            $table->string('father_name', 100)->nullable();
            $table->string('father_place_of_birth', 100)->nullable();
            $table->date('father_date_of_birth')->nullable();
            $table->string('father_address', 100)->nullable();
            $table->string('father_phone_number', 13)->nullable();
            $table->enum('father_religion', ['1', '2', '3', '4', '5', '6', '7'])->nullable();
            $table->string('father_city', 100)->nullable();
            $table->string('father_last_education', 25)->nullable();
            $table->string('father_job', 100)->nullable();
            $table->string('father_income', 100)->nullable();
            // parent information mother
            $table->string('nik_mother', 16)->nullable();
            $table->string('mother_name', 100)->nullable();
            $table->string('mother_place_of_birth', 100)->nullable();
            $table->date('mother_date_of_birth')->nullable();
            $table->string('mother_address', 100)->nullable();
            $table->string('mother_phone_number', 13)->nullable();
            $table->enum('mother_religion', ['1', '2', '3', '4', '5', '6', '7'])->nullable();
            $table->string('mother_city', 100)->nullable();
            $table->string('mother_last_education', 25)->nullable();
            $table->string('mother_job', 100)->nullable();
            $table->string('mother_income', 100)->nullable();
            // parent information guardian
            $table->string('nik_guardian', 16)->nullable();;
            $table->string('guardian_name', 100)->nullable();;
            $table->string('guardian_place_of_birth', 100)->nullable();
            $table->date('guardian_date_of_birth')->nullable();
            $table->string('guardian_address', 100)->nullable();
            $table->string('guardian_phone_number', 13)->nullable();
            $table->enum('guardian_religion', ['1', '2', '3', '4', '5', '6', '7'])->nullable();
            $table->string('guardian_city', 100)->nullable();
            $table->string('guardian_last_education', 25)->nullable();
            $table->string('guardian_job', 100)->nullable();
            $table->string('guardian_income', 100)->nullable();

            // student medical condition information
            $table->string('height')->nullable();
            $table->string('weight')->nullable();
            $table->string('special_treatment')->nullable();
            $table->string('note_health')->nullable();

            // previeously formal school
            $table->date('old_school_entry_date')->nullable();
            $table->date('old_school_leaving_date')->nullable();
            $table->string('old_school_name', 100)->nullable();
            $table->string('old_school_achivements', 100)->nullable();
            $table->string('old_school_achivements_year', 100)->nullable();
            $table->string('certificate_number_old_school', 100)->nullable();
            $table->string('old_school_address', 100)->nullable();
            $table->string('no_sttb')->nullable();
            $table->unsignedInteger('nem')->nullable();

            $table->string('photo')->nullable();
            $table->string('photo_document_health')->nullable();
            $table->string('photo_list_questions')->nullable();
            $table->string('photo_document_old_school')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
