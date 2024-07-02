<?php

namespace App\Filament\Resources\MasterData\ClassSchoolResource\Widgets;

use App\Models\ClassSchool;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class TotalMemberClassSchoolInClassSchoolChart extends ApexChartWidget
{
    protected static ?string $chartId = 'TotalMemberClassSchoolInClassSchoolChart';
    protected static ?string $heading = 'Total Members in Each Class School';

    protected function getOptions(): array
    {
        // Fetch each ClassSchool with the count of its members
        $classSchools = ClassSchool::withCount('memberClassSchools')->get();

        // Extract the counts and names for the chart
        $data = $classSchools->pluck('member_class_schools_count')->all();
        $labels = $classSchools->pluck('name')->all();  // Assuming 'name' is a column in ClassSchool

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'Total Members',
                    'data' => $data,
                ],
            ],
            'xaxis' => [
                'categories' => $labels,
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'colors' => ['#1353c4'],
            'stroke' => [
                'curve' => 'smooth',
            ],
            'dataLabels' => [
                'enabled' => false,
            ],
        ];
    }
}
