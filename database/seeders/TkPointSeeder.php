<?php

namespace Database\Seeders;

use App\Models\TkPoint;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TkPointSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TkPoint::create([
            'tk_topic_id' => 1,
            'tk_subtopic_id' => 1,
            'term_id' => 1,
            'name' => 'Listens attentively without interruption',
        ]);
        TkPoint::create([
            'tk_topic_id' => 1,
            'tk_subtopic_id' => 1,
            'term_id' => 1,
            'name' => 'Comprehends lessons and stories listened to',
        ]);

        TkPoint::create([
            'tk_topic_id' => 1,
            'tk_subtopic_id' => 2,
            'term_id' => 1,
            'name' => 'Names objects and actions',
        ]);
        TkPoint::create([
            'tk_topic_id' => 1,
            'tk_subtopic_id' => 2,
            'term_id' => 1,
            'name' => 'Expresses own thoughts and feelings using language',
        ]);
        TkPoint::create([
            'tk_topic_id' => 1,
            'tk_subtopic_id' => 2,
            'term_id' => 1,
            'name' => 'Speaks clearly and responds to questions appropriately',
        ]);
        TkPoint::create([
            'tk_topic_id' => 1,
            'tk_subtopic_id' => 2,
            'term_id' => 1,
            'name' => 'Contributes to discussions and lessons',
        ]);
        TkPoint::create([
            'tk_topic_id' => 1,
            'tk_subtopic_id' => 2,
            'name' => 'Knows a range of songs, rhymes and chants',
        ]);
        TkPoint::create([
            'tk_topic_id' => 1,
            'tk_subtopic_id' => 2,
            'term_id' => 1,
            'name' => 'Performs Show and Tell confidently',
        ]);

        TkPoint::create([
            'tk_topic_id' => 2,
            'tk_subtopic_id' => 3,
            'term_id' => 1,
            'name' => 'Performs Show and Tell confidently (Topic 2)',
        ]);
        TkPoint::create([
            'tk_topic_id' => 2,
            'tk_subtopic_id' => 3,
            'name' => 'Recognizes letter names of n, o, p, q, r',
        ]);
        TkPoint::create([
            'tk_topic_id' => 2,
            'tk_subtopic_id' => 3,
            'term_id' => 1,
            'name' => 'Reads and tells the phonetic sounds of the alphabet learned: n, o, p, q, r',
        ]);
        TkPoint::create([
            'tk_topic_id' => 2,
            'tk_subtopic_id' => 3,
            'term_id' => 1,
            'name' => 'Recognizes & reads own name',
        ]);

        TkPoint::create([
            'tk_topic_id' => 2,
            'tk_subtopic_id' => 4,
            'term_id' => 1,
            'name' => 'Traces letters of the alphabet',
        ]);
        TkPoint::create([
            'tk_topic_id' => 2,
            'tk_subtopic_id' => 4,
            'term_id' => 1,
            'name' => 'Traces own name legibly',
        ]);

        TkPoint::create([
            'tk_topic_id' => 3,
            'tk_subtopic_id' => null,
            'term_id' => 1,
            'name' => 'Comprehends stories listened to',
        ]);
        TkPoint::create([
            'tk_topic_id' => 3,
            'tk_subtopic_id' => null,
            'term_id' => 1,
            'name' => 'Knows letter names and sounds of o, p, r, s, t, u',
        ]);
        TkPoint::create([
            'tk_topic_id' => 3,
            'tk_subtopic_id' => null,
            'term_id' => 1,
            'name' => 'Expresses thoughts and ideas during discussions',
        ]);
        TkPoint::create([
            'tk_topic_id' => 3,
            'tk_subtopic_id' => null,
            'term_id' => 1,
            'name' => 'Recognizes picture names',
        ]);
        TkPoint::create([
            'tk_topic_id' => 3,
            'tk_subtopic_id' => null,
            'term_id' => 1,
            'name' => 'Follows directions in accomplishing tasks',
        ]);
        TkPoint::create([
            'tk_topic_id' => 3,
            'tk_subtopic_id' => null,
            'term_id' => 1,
            'name' => 'Accomplishes task independently',
        ]);

        TkPoint::create([
            'tk_topic_id' => 4,
            'tk_subtopic_id' => 5,
            'term_id' => 1,
            'name' => 'Recognizes numerals and traces from 1 to 10',
        ]);

        TkPoint::create([
            'tk_topic_id' => 4,
            'tk_subtopic_id' => 6,
            'term_id' => 1,
            'name' => 'Rote counts number forward from 1 to 10',
        ]);
        TkPoint::create([
            'tk_topic_id' => 4,
            'tk_subtopic_id' => 6,
            'term_id' => 1,
            'name' => 'Counts objects using onetoone correspondence from 6 to 10',
        ]);
        TkPoint::create([
            'tk_topic_id' => 4,
            'tk_subtopic_id' => 6,
            'term_id' => 1,
            'name' => 'Orders numerals in ascending order up to 10',
        ]);
        TkPoint::create([
            'tk_topic_id' => 4,
            'tk_subtopic_id' => 6,
            'term_id' => 1,
            'name' => 'Matches numbers in words to numerals up to 10',
        ]);

        TkPoint::create([
            'tk_topic_id' => 4,
            'tk_subtopic_id' => 7,
            'term_id' => 1,
            'name' => 'Recognizes the 2D shapes learned',
        ]);
        TkPoint::create([
            'tk_topic_id' => 4,
            'tk_subtopic_id' => 7,
            'term_id' => 1,
            'name' => 'Classifies or sorts objects according to shapes and colours',
        ]);

        TkPoint::create([
            'tk_topic_id' => 5,
            'tk_subtopic_id' => null,
            'term_id' => 1,
            'name' => 'Shows curiosity and interest in topics covered',
        ]);
        TkPoint::create([
            'tk_topic_id' => 5,
            'tk_subtopic_id' => null,
            'term_id' => 1,
            'name' => 'Investigates and interacts during discussion',
        ]);
        TkPoint::create([
            'tk_topic_id' => 5,
            'tk_subtopic_id' => null,
            'term_id' => 1,
            'name' => 'Develops understanding and acquisition of knowledge',
        ]);
        TkPoint::create([
            'tk_topic_id' => 5,
            'tk_subtopic_id' => null,
            'term_id' => 1,
            'name' => 'Shows interest in the environment',
        ]);
        TkPoint::create([
            'tk_topic_id' => 5,
            'tk_subtopic_id' => null,
            'term_id' => 1,
            'name' => 'Applies the knowledge learnt in daily routines',
        ]);
    }
}
