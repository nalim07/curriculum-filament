<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // siswas
        $siswa = User::create([
            'username' => 'siswa',
            'email' => 'siswa@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('123456'),
            'status' => true,
        ]);
        $siswa->assignRole('student');
        Student::create([
            'user_id' => $siswa->id,
            'class_school_id' => 8,
            'level_id' => 5,
            'line_id' => 1,
            'registration_type' => '1',
            'entry_year' => '2022',
            'entry_semester' => '1',
            'entry_class' => 'PA1',

            'nis' => '192007007',
            'nisn' => '0987654321',
            'fullname' => 'John Doe 1',
            'username' => 'John',
            'nik' => '1234567890123421',
            'email' => 'john.doe1@example.com',
            'phone_number' => '1234567891',
            'gender' => '1',
            'blood_type' => 'A',
            'religion' => '1',
            'place_of_birth' => 'Jakarta',
            'date_of_birth' => '2000-01-01',
            'anak_ke' => '01',
            'number_of_sibling' => '02',
            'citizen' => 'ID',
            'address' => 'Jl. ABC No. 123',
            'city' => 'Jakarta',
            'postal_code' => 12345,
            'distance_home_to_school' => 5,
            'living_together' => '1',
            'transportation' => 'Car',

            'father_name' => 'Dad Doe',
            'mother_name' => 'Mom Doe',
            'guardian_name' => 'Wali Doe',
            'nik_father' => '1234567890123454',
            'nik_mother' => '1234567890123455',
            'nik_guardian' => '1234567890123456',
            'email_parent' => 'dad.doe1@example.com',
            'father_phone_number' => '1234567891',
            'mother_phone_number' => '1234567892',
            'guardian_phone_number' => '1234567893',
            'father_job' => 'Engineer',
            'mother_job' => 'Teacher',
            'guardian_job' => 'Doctor',
            'father_address' => 'Jl. ABC No. 123',
            'mother_address' => 'Jl. ABC No. 123',
            'guardian_address' => 'Jl. ABC No. 123',

            'height' => 170,
            'weight' => 60,
            'special_treatment' => 'None',
            'note_health' => 'Healthy individual',
            'photo_document_health' => 'health_document.pdf',
            'photo_list_questions' => 'questionnaire.pdf',

            'old_school_entry_date' => '2000-01-01',
            'old_school_leaving_date' => '2000-01-01',
            'old_school_name' => 'School test',
            'old_school_achivements' => 'School Championship',
            'old_school_achivements_year' => '2000',
            'certificate_number_old_school' => 'ABC123',
            'old_school_address' => 'Jl. XYZ No. 456',
            'no_sttb' => 'STTB123',
            'nem' => 8.75,
            'photo_document_old_school' => 'previous_school_document.pdf',

            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $siswa = User::create([
            'username' => 'siswa3',
            'email' => 'siswa3@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('123456'),
            'status' => true,
        ]);
        $siswa->assignRole('student');
        Student::create([
            'user_id' => $siswa->id,
            'class_school_id' => 8,
            'level_id' => 1,
            'line_id' => 3,
            'registration_type' => '1',
            'entry_year' => '2022',
            'entry_semester' => '1',
            'entry_class' => 'PG',

            'nis' => '1234567891',
            'nisn' => '0987654322',
            'fullname' => 'John Doe 2',
            'username' => 'John',
            'nik' => '1234567890123456',
            'email' => 'john.doe@example.com',
            'phone_number' => '1234567890',
            'gender' => '1',
            'blood_type' => 'A',
            'religion' => '1',
            'place_of_birth' => 'Jakarta',
            'date_of_birth' => '2000-01-01',
            'anak_ke' => '01',
            'number_of_sibling' => '02',
            'citizen' => 'ID',
            'address' => 'Jl. ABC No. 123',
            'city' => 'Jakarta',
            'postal_code' => 12345,
            'distance_home_to_school' => 5,
            'living_together' => '1',
            'transportation' => 'Car',

            'father_name' => 'Dad Doe',
            'mother_name' => 'Mom Doe',
            'guardian_name' => 'Wali Doe',
            'nik_father' => '1234567890123451',
            'nik_mother' => '1234567890123452',
            'nik_guardian' => '1234567890123453',
            'email_parent' => 'dad.do1e1@example.com',
            'father_phone_number' => '1234567890',
            'mother_phone_number' => '1234567890',
            'guardian_phone_number' => '1234567890',
            'father_job' => 'Engineer',
            'mother_job' => 'Teacher',
            'guardian_job' => 'Doctor',

            'height' => 170,
            'weight' => 60,
            'special_treatment' => 'None',
            'note_health' => 'Healthy individual',
            'photo_document_health' => 'health_document.pdf',
            'photo_list_questions' => 'questionnaire.pdf',

            'old_school_entry_date' => '2000-01-01',
            'old_school_leaving_date' => '2000-01-01',
            'old_school_name' => 'School test',
            'old_school_achivements' => 'School Championship',
            'old_school_achivements_year' => '2000',
            'certificate_number_old_school' => 'ABC123',
            'old_school_address' => 'Jl. XYZ No. 456',
            'no_sttb' => 'STTB123',
            'nem' => 8.75,
            'photo_document_old_school' => 'previous_school_document.pdf',

            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
