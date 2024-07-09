<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PlanFormatifValue;
use App\Models\PlanFormatifValueTechnique;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PlanFormatifValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ////semester 1
        PlanFormatifValue::create([
            'learning_data_id' => 1,
            'semester_id' => 1,
            'term_id' => 1,
        ]);

        PlanFormatifValueTechnique::create([
            'plan_formatif_value_id' => 1,
            'code' => '1',
            'technique' => '1',
            'weighting' => '70',
        ]);

        PlanFormatifValueTechnique::create([
            'plan_formatif_value_id' => 1,
            'code' => '2',
            'technique' => '2',
            'weighting' => '70',
        ]);

        PlanFormatifValueTechnique::create([
            'plan_formatif_value_id' => 1,
            'code' => '3',
            'technique' => '3',
            'weighting' => '70',
        ]);

        PlanFormatifValueTechnique::create([
            'plan_formatif_value_id' => 1,
            'code' => '4',
            'technique' => '4',
            'weighting' => '70',
        ]);

        PlanFormatifValueTechnique::create([
            'plan_formatif_value_id' => 1,
            'code' => '5',
            'technique' => '5',
            'weighting' => '70',
        ]);


        // index 2
        PlanFormatifValue::create([
            'learning_data_id' => 1,
            'semester_id' => 1,
            'term_id' => 2,
        ]);

        PlanFormatifValueTechnique::create([
            'plan_formatif_value_id' => 2,
            'code' => '1',
            'technique' => '1',
            'weighting' => '75',
        ]);

        PlanFormatifValueTechnique::create([
            'plan_formatif_value_id' => 2,
            'code' => '2',
            'technique' => '2',
            'weighting' => '75',
        ]);

        PlanFormatifValueTechnique::create([
            'plan_formatif_value_id' => 2,
            'code' => '3',
            'technique' => '3',
            'weighting' => '75',
        ]);

        PlanFormatifValueTechnique::create([
            'plan_formatif_value_id' => 2,
            'code' => '4',
            'technique' => '4',
            'weighting' => '70',
        ]);

        PlanFormatifValueTechnique::create([
            'plan_formatif_value_id' => 2,
            'code' => '5',
            'technique' => '5',
            'weighting' => '70',
        ]);

        // subject2
        PlanFormatifValue::create([
            'learning_data_id' => 2,
            'semester_id' => 1,
            'term_id' => 1,
        ]);

        PlanFormatifValueTechnique::create([
            'plan_formatif_value_id' => 3,
            'code' => '1',
            'technique' => '1',
            'weighting' => '70',
        ]);

        PlanFormatifValueTechnique::create([
            'plan_formatif_value_id' => 3,
            'code' => '2',
            'technique' => '2',
            'weighting' => '70',
        ]);

        PlanFormatifValueTechnique::create([
            'plan_formatif_value_id' => 3,
            'code' => '3',
            'technique' => '3',
            'weighting' => '70',
        ]);

        PlanFormatifValueTechnique::create([
            'plan_formatif_value_id' => 3,
            'code' => '4',
            'technique' => '4',
            'weighting' => '70',
        ]);

        PlanFormatifValueTechnique::create([
            'plan_formatif_value_id' => 3,
            'code' => '5',
            'technique' => '5',
            'weighting' => '70',
        ]);

        // index 2
        PlanFormatifValue::create([
            'learning_data_id' => 2,
            'semester_id' => 1,
            'term_id' => 2,
        ]);

        PlanFormatifValueTechnique::create([
            'plan_formatif_value_id' => 4,
            'code' => '1',
            'technique' => '1',
            'weighting' => '75',
        ]);

        PlanFormatifValueTechnique::create([
            'plan_formatif_value_id' => 4,
            'code' => '2',
            'technique' => '2',
            'weighting' => '75',
        ]);

        PlanFormatifValueTechnique::create([
            'plan_formatif_value_id' => 4,
            'code' => '3',
            'technique' => '3',
            'weighting' => '75',
        ]);

        PlanFormatifValueTechnique::create([
            'plan_formatif_value_id' => 4,
            'code' => '4',
            'technique' => '4',
            'weighting' => '70',
        ]);

        PlanFormatifValueTechnique::create([
            'plan_formatif_value_id' => 4,
            'code' => '5',
            'technique' => '5',
            'weighting' => '70',
        ]);

        ////semester 2
        PlanFormatifValue::create([
            'learning_data_id' => 1,
            'semester_id' => 2,
            'term_id' => 1,
        ]);

        PlanFormatifValueTechnique::create([
            'plan_formatif_value_id' => 5,
            'code' => '1',
            'technique' => '1',
            'weighting' => '70',
        ]);

        PlanFormatifValueTechnique::create([
            'plan_formatif_value_id' => 5,
            'code' => '2',
            'technique' => '2',
            'weighting' => '70',
        ]);

        PlanFormatifValueTechnique::create([
            'plan_formatif_value_id' => 5,
            'code' => '3',
            'technique' => '3',
            'weighting' => '70',
        ]);

        PlanFormatifValueTechnique::create([
            'plan_formatif_value_id' => 5,
            'code' => '4',
            'technique' => '4',
            'weighting' => '70',
        ]);

        PlanFormatifValueTechnique::create([
            'plan_formatif_value_id' => 5,
            'code' => '5',
            'technique' => '5',
            'weighting' => '70',
        ]);

        // index 2
        PlanFormatifValue::create([
            'learning_data_id' => 1,
            'semester_id' => 2,
            'term_id' => 2,
        ]);

        PlanFormatifValueTechnique::create([
            'plan_formatif_value_id' => 6,
            'code' => '1',
            'technique' => '1',
            'weighting' => '75',
        ]);

        PlanFormatifValueTechnique::create([
            'plan_formatif_value_id' => 6,
            'code' => '2',
            'technique' => '2',
            'weighting' => '75',
        ]);

        PlanFormatifValueTechnique::create([
            'plan_formatif_value_id' => 6,
            'code' => '3',
            'technique' => '3',
            'weighting' => '75',
        ]);

        PlanFormatifValueTechnique::create([
            'plan_formatif_value_id' => 6,
            'code' => '4',
            'technique' => '4',
            'weighting' => '70',
        ]);

        PlanFormatifValueTechnique::create([
            'plan_formatif_value_id' => 6,
            'code' => '5',
            'technique' => '5',
            'weighting' => '70',
        ]);

        // subject2
        PlanFormatifValue::create([
            'learning_data_id' => 2,
            'semester_id' => 2,
            'term_id' => 1,
        ]);

        PlanFormatifValueTechnique::create([
            'plan_formatif_value_id' => 7,
            'code' => '1',
            'technique' => '1',
            'weighting' => '70',
        ]);

        PlanFormatifValueTechnique::create([
            'plan_formatif_value_id' => 7,
            'code' => '2',
            'technique' => '2',
            'weighting' => '70',
        ]);

        PlanFormatifValueTechnique::create([
            'plan_formatif_value_id' => 7,
            'code' => '3',
            'technique' => '3',
            'weighting' => '70',
        ]);

        PlanFormatifValueTechnique::create([
            'plan_formatif_value_id' => 7,
            'code' => '4',
            'technique' => '4',
            'weighting' => '70',
        ]);

        PlanFormatifValueTechnique::create([
            'plan_formatif_value_id' => 7,
            'code' => '5',
            'technique' => '5',
            'weighting' => '70',
        ]);

        // index 2
        PlanFormatifValue::create([
            'learning_data_id' => 2,
            'semester_id' => 2,
            'term_id' => 2,
        ]);

        PlanFormatifValueTechnique::create([
            'plan_formatif_value_id' => 8,
            'code' => '1',
            'technique' => '1',
            'weighting' => '75',
        ]);

        PlanFormatifValueTechnique::create([
            'plan_formatif_value_id' => 8,
            'code' => '2',
            'technique' => '2',
            'weighting' => '75',
        ]);

        PlanFormatifValueTechnique::create([
            'plan_formatif_value_id' => 8,
            'code' => '3',
            'technique' => '3',
            'weighting' => '75',
        ]);

        PlanFormatifValueTechnique::create([
            'plan_formatif_value_id' => 8,
            'code' => '4',
            'technique' => '4',
            'weighting' => '70',
        ]);

        PlanFormatifValueTechnique::create([
            'plan_formatif_value_id' => 8,
            'code' => '5',
            'technique' => '5',
            'weighting' => '70',
        ]);
    }
}
