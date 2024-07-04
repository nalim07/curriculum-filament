<?php

namespace Database\Seeders;

use App\Models\TkSubtopic;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TkSubtopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TkSubtopic::create([
            'tk_topic_id' => 1,
            'name' => 'Listening',
        ]);
        TkSubtopic::create([
            'tk_topic_id' => 1,
            'name' => 'Language for communication and thinking',
        ]);
        TkSubtopic::create([
            'tk_topic_id' => 2,
            'name' => 'Reading',
        ]);

        TkSubtopic::create([
            'tk_topic_id' => 2,
            'name' => 'Writing',
        ]);

        // topid_id 3 BAHASA INDONESIA not have subtopics, just point after topic

        TkSubtopic::create([
            'tk_topic_id' => 4,
            'name' => 'Numbers as labels and for counting',
        ]);
        TkSubtopic::create([
            'tk_topic_id' => 4,
            'name' => 'Calculating',
        ]);
        TkSubtopic::create([
            'tk_topic_id' => 4,
            'name' => 'Shape, size and measures	',
        ]);

        // topid_id 5 SCIENCE not have subtopics, just point after topic

    }
}
