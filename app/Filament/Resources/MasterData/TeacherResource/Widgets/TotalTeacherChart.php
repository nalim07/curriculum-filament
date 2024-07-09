<?php

namespace App\Filament\Resources\MasterData\TeacherResource\Widgets;

use App\Models\Teacher;
use App\Models\EmployeeUnit;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class TotalTeacherChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'totalTeacher';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Total Teacher by Unit';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        $data = $this->getTeacherCountsByUnit();

        return [
            'chart' => [
                'type' => 'donut',
                'height' => 300,
            ],
            'series' => $data['counts'],
            'labels' => $data['units'],
            'colors' => $this->generateColors(count($data['units'])),
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

    private function getTeacherCountsByUnit(): array
    {
        $units = EmployeeUnit::whereHas('employee', function ($query) {
            $query->whereHas('teacher', function ($query) {
                $query;
            });
        })->orderBy('id')->pluck('name')->toArray();
        $counts = [];

        foreach ($units as $unit) {
            $count = Teacher::whereHas('employee', function ($query) use ($unit) {
                $query->whereHas('employeeUnit', function ($query) use ($unit) {
                    $query->where('name', $unit);
                });
            })->count();
            $counts[] = $count;
        }
        return ['units' => $units, 'counts' => $counts];
    }

    private function generateColors(int $count): array
    {
        $colors = [];
        $steps = intval(360 / $count);

        for ($i = 0; $i < 360; $i += $steps) {
            $hue = $i;
            $saturation = 90 + rand(0, 10); // randomize a bit
            $lightness = 50 + rand(0, 10); // randomize a bit

            $colors[] = "hsl($hue, {$saturation}%, {$lightness}%)";
        }

        return $colors;
    }
}
