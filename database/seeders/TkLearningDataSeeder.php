<?php

namespace Database\Seeders;

use App\Models\TkLearningData;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TkLearningDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TkLearningData::create([
            'tk_topic_id' => 1,
            'teacher_id' => 1,
            'level_id' => 1,
            'class_school_id' => 1,
        ]);
    }
}
