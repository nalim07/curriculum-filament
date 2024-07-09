<?php

namespace Database\Seeders;

use App\Models\LearningData;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LearningDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        LearningData::create([
            'class_school_id' => 8,
            'subject_id' => 1,
            'teacher_id' => 1,
            'status' => true
        ]);

        LearningData::create([
            'class_school_id' => 8,
            'subject_id' => 2,
            'teacher_id' => 2,
            'status' => true
        ]);

        LearningData::create([
            'class_school_id' => 8,
            'subject_id' => 3,
            'teacher_id' => 3,
            'status' => true
        ]);

        LearningData::create([
            'class_school_id' => 9,
            'subject_id' => 1,
            'teacher_id' => 3,
            'status' => true
        ]);
    }
}
