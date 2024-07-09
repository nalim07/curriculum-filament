<?php

namespace Database\Seeders;

use App\Models\StudentAttendance;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentAttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StudentAttendance::create([
            'class_school_id' => 8,
            'semester_id' => 1,
            'member_class_school_id' => 1,
            'sick' => 1,
            'permission' => 1,
            'without_explanation' => 1,
        ]);

        StudentAttendance::create([
            'class_school_id' => 8,
            'semester_id' => 1,
            'member_class_school_id' => 2,
            'sick' => 1,
            'permission' => 1,
            'without_explanation' => 0,
        ]);
    }
}
