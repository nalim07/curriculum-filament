<?php

namespace App\Filament\Resources\MasterData\ClassSchoolResource\Widgets;

use App\Models\ClassSchool;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class TotalMemberClassSchoolInClassSchoolChart extends ApexChartWidget
{
    protected static ?string $chartId = 'TotalMemberClassSchoolInClassSchoolChart';
    protected static ?string $heading = 'Total Members in Each Class School';
    protected static ?int $sort = 3;

    protected function getOptions(): array
    {
        $classSchools = ClassSchool::withCount('memberClassSchools')->get();
        $data = $classSchools->pluck('member_class_schools_count')->all();
        $labels = $classSchools->pluck('name')->all();

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
