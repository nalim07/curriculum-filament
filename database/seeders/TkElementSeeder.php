<?php

namespace Database\Seeders;

use App\Models\TkElement;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TkElementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TkElement::create([
            'level_id' => 1,
            'name' => 'COMMUNICATION, LANGUAGES AND LITERACY',
        ]);
        TkElement::create([
            'level_id' => 1,
            'name' => 'PROBLEM SOLVING, REASONING AND NUMERACY',
        ]);
        TkElement::create([
            'level_id' => 1,
            'name' => 'KNOWLEDGE AND UNDERSTANDING OF THE WORLD',
        ]);
        TkElement::create([
            'level_id' => 2,
            'name' => 'KNOWLEDGE AND UNDERSTANDING OF THE WORLD',
        ]);
    }
}
