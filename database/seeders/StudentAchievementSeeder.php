<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StudentAchievement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class StudentAchievementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StudentAchievement::create([
            'class_school_id' => 8,
            'semester_id' => 1,
            'member_class_school_id' => 1,
            'name' => 'Test',
            'type_of_achievement' => 1,
            'level_achievement' => 1,
            'description' => 'Test',
        ]);

        StudentAchievement::create([
            'class_school_id' => 8,
            'semester_id' => 1,
            'member_class_school_id' => 2,
            'name' => 'Test',
            'type_of_achievement' => 2,
            'level_achievement' => 3,
            'description' => 'Test',
        ]);
    }
}
