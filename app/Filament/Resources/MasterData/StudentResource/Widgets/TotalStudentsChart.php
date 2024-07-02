<?php

namespace App\Filament\Resources\MasterData\StudentResource\Widgets;

use App\Models\Level;
use App\Models\Student;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class TotalStudentsChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'studentsChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Total Student';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
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
                    'useSeriesColors' => false
                ],
                'markers' => [
                    'width' => 12,
                    'height' => 12
                ],
                'fontFamily' => 'inherit',
                'itemMargin' => [
                    'horizontal' => 10,
                    'vertical' => 5
                ]
            ],
            'responsive' => [
                [
                    'breakpoint' => 480,
                    'options' => [
                        'chart' => [
                            'width' => 300
                        ],
                        'legend' => [
                            'position' => 'bottom'
                        ]
                    ]
                ]
            ]
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
