<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HomeroomNotes;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class HomeroomNotesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        HomeroomNotes::create([
            'semester_id' => 1,
            'class_school_id' => 8,
            'member_class_school_id' => 1,
            'notes' => 'test',
        ]);

        HomeroomNotes::create([
            'semester_id' => 1,
            'class_school_id' => 8,
            'member_class_school_id' => 2,
            'notes' => 'test',
        ]);
    }
}
