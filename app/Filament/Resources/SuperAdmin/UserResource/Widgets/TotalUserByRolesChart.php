<?php

namespace App\Filament\Resources\SuperAdmin\UserResource\Widgets;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class TotalUserByRolesChart extends ApexChartWidget
{
    protected static ?string $chartId = 'totalUserByRolesChart';
    protected static ?int $sort = 4;
    protected static ?string $heading = 'Total User By Roles';

    protected function getOptions(): array
    {
        // Query to get the number of users per role
        $rolesCounts = Role::select('roles.name', DB::raw('count(model_has_roles.model_id) as total'))
            ->leftJoin('model_has_roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('model_has_roles.model_type', User::class)
            ->groupBy('roles.id', 'roles.name') // Include 'roles.name' in the GROUP BY clause
            ->get();

        $seriesData = $rolesCounts->pluck('total');
        $categories = $rolesCounts->pluck('name');

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'Users',
                    'data' => $seriesData,
                ],
            ],
            'xaxis' => [
                'categories' => $categories,
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
