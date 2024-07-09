<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PlanSumatifValue;
use App\Models\PlanSumatifValueTechnique;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PlanSumatifValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PlanSumatifValue::create([
            'learning_data_id' => 1,
            'semester_id' => 1,
            'term_id' => 1,
        ]);

        PlanSumatifValueTechnique::create([
            'plan_sumatif_value_id' => 1,
            'code' => '1',
            'technique' => '1',
            'weighting' => '70',
        ]);

        PlanSumatifValueTechnique::create([
            'plan_sumatif_value_id' => 1,
            'code' => '2',
            'technique' => '2',
            'weighting' => '70',
        ]);

        // index 2
        PlanSumatifValue::create([
            'learning_data_id' => 1,
            'semester_id' => 1,
            'term_id' => 2,
        ]);

        PlanSumatifValueTechnique::create([
            'plan_sumatif_value_id' => 2,
            'code' => '1',
            'technique' => '1',
            'weighting' => '75',
        ]);

        PlanSumatifValueTechnique::create([
            'plan_sumatif_value_id' => 2,
            'code' => '2',
            'technique' => '2',
            'weighting' => '75',
        ]);

        // subject2
        PlanSumatifValue::create([
            'learning_data_id' => 2,
            'semester_id' => 1,
            'term_id' => 1,
        ]);

        PlanSumatifValueTechnique::create([
            'plan_sumatif_value_id' => 3,
            'code' => '1',
            'technique' => '1',
            'weighting' => '70',
        ]);

        PlanSumatifValueTechnique::create([
            'plan_sumatif_value_id' => 3,
            'code' => '2',
            'technique' => '2',
            'weighting' => '70',
        ]);

        // index 2
        PlanSumatifValue::create([
            'learning_data_id' => 2,
            'semester_id' => 1,
            'term_id' => 2,
        ]);

        PlanSumatifValueTechnique::create([
            'plan_sumatif_value_id' => 4,
            'code' => '1',
            'technique' => '1',
            'weighting' => '75',
        ]);

        PlanSumatifValueTechnique::create([
            'plan_sumatif_value_id' => 4,
            'code' => '2',
            'technique' => '2',
            'weighting' => '75',
        ]);

        ////semester 2
        PlanSumatifValue::create([
            'learning_data_id' => 1,
            'semester_id' => 2,
            'term_id' => 1,
        ]);

        PlanSumatifValueTechnique::create([
            'plan_sumatif_value_id' => 5,
            'code' => '1',
            'technique' => '1',
            'weighting' => '70',
        ]);

        PlanSumatifValueTechnique::create([
            'plan_sumatif_value_id' => 5,
            'code' => '2',
            'technique' => '2',
            'weighting' => '70',
        ]);

        // index 2
        PlanSumatifValue::create([
            'learning_data_id' => 1,
            'semester_id' => 2,
            'term_id' => 2,
        ]);

        PlanSumatifValueTechnique::create([
            'plan_sumatif_value_id' => 6,
            'code' => '1',
            'technique' => '1',
            'weighting' => '75',
        ]);

        PlanSumatifValueTechnique::create([
            'plan_sumatif_value_id' => 6,
            'code' => '2',
            'technique' => '2',
            'weighting' => '75',
        ]);

        // subject2
        PlanSumatifValue::create([
            'learning_data_id' => 2,
            'semester_id' => 2,
            'term_id' => 1,
        ]);

        PlanSumatifValueTechnique::create([
            'plan_sumatif_value_id' => 7,
            'code' => '1',
            'technique' => '1',
            'weighting' => '70',
        ]);

        PlanSumatifValueTechnique::create([
            'plan_sumatif_value_id' => 7,
            'code' => '2',
            'technique' => '2',
            'weighting' => '70',
        ]);

        // index 2
        PlanSumatifValue::create([
            'learning_data_id' => 2,
            'semester_id' => 2,
            'term_id' => 2,
        ]);

        PlanSumatifValueTechnique::create([
            'plan_sumatif_value_id' => 8,
            'code' => '1',
            'technique' => '1',
            'weighting' => '75',
        ]);

        PlanSumatifValueTechnique::create([
            'plan_sumatif_value_id' => 8,
            'code' => '2',
            'technique' => '2',
            'weighting' => '75',
        ]);
    }
}
