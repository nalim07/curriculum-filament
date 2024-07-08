<?php

namespace App\Filament\Resources\MasterData\StudentResource\Widgets;

use App\Models\Level;
use App\Models\Student;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class TotalStudentsChart extends ApexChartWidget
{
    protected static ?string $chartId = 'studentsChart';
    protected static ?int $sort = 1;
    protected static ?string $heading = 'Total Student';

    protected function getOptions(): array
    {
        $data = $this->getStudentCountsByLevel();

        return [
            'chart' => [
                'type' => 'donut',
                'height' => 300,
            ],
            'series' => $data['counts'],
            'labels' => $data['levels'],
            'legend' => [
                'show' => true,
                'position' => 'bottom',
                'horizontalAlign' => 'center',
                'labels' => [
                    'colors' => ['gray'],
                    'useSeriesColors' => false,
                ],
                'markers' => [
                    'width' => 12,
                    'height' => 12,
                ],
                'fontFamily' => 'inherit',
                'itemMargin' => [
                    'horizontal' => 10,
                    'vertical' => 5,
                ],
            ],
            'responsive' => [
                [
                    'breakpoint' => 480,
                    'options' => [
                        'chart' => [
                            'width' => 300,
                        ],
                        'legend' => [
                            'position' => 'bottom',
                        ],
                    ],
                ],
            ],
        ];
    }

    private function getStudentCountsByLevel(): array
    {
        $levels = Level::get()->sortBy('id')->pluck('name')->toArray();
        $counts = [];

        foreach ($levels as $level) {
            $count = Student::whereHas('level', function ($query) use ($level) {
                $query->where('name', $level);
            })->count();
            $counts[] = $count;
        }

        return ['levels' => $levels, 'counts' => $counts];
    }
}
