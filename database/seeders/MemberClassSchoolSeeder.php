<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MemberClassSchool;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MemberClassSchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MemberClassSchool::create([
            'student_id' => 1,
            'class_school_id' => 8,
            'academic_year_id' => 1,
            'registration_type' => 1,
        ]);

        MemberClassSchool::create([
            'student_id' => 2,
            'class_school_id' => 8,
            'academic_year_id' => 1,
            'registration_type' => 1,
        ]);

        MemberClassSchool::create([
            'student_id' => 3,
            'class_school_id' => 1,
            'academic_year_id' => 1,
            'registration_type' => 1,
        ]);
    }
}
