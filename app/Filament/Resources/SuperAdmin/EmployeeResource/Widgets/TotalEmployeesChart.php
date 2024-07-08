<?php

namespace App\Filament\Resources\SuperAdmin\EmployeeResource\Widgets;

use App\Models\Teacher;
use App\Models\Employee;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class TotalEmployeesChart extends ApexChartWidget
{
    protected static ?string $chartId = 'totalEmployeesChart';
    protected static ?int $sort = 2;
    protected static ?string $heading = 'Total Employee & Teacher';

    protected function getOptions(): array
    {
        $employeeCount = Employee::count();
        $teacherCount = Teacher::count();

        return [
            'chart' => [
                'type' => 'donut',
                'height' => 300,
            ],
            'series' => [$employeeCount, $teacherCount],
            'labels' => ['Employees', 'Teachers'],
            'legend' => [
                'show' => true,
                'position' => 'bottom',
                'horizontalAlign' => 'center',
                'labels' => [
                    'colors' => ['gray'],
                    'useSeriesColors' => false
                ],
                'fontFamily' => 'inherit',
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
}
