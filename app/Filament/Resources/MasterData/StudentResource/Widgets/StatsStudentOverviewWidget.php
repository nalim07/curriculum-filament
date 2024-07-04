<?php

namespace App\Filament\Resources\MasterData\StudentResource\Widgets;

use App\Models\Student;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsStudentOverviewWidget extends BaseWidget
{
    protected static string $view = 'filament.widgets.student-stats-overview';

    protected function getStats(): array
    {
        return [
            Card::make('Level 1 Students', Student::where('level_id', 1)->count()),
            Card::make('Level 2 Students', Student::where('level_id', 2)->count()),
            Card::make('Level 3 Students', Student::where('level_id', 3)->count()),
            Card::make('Level 4 Students', Student::where('level_id', 4)->count()),
            Card::make('Level 5 Students', Student::where('level_id', 5)->count()),
            Card::make('Level 6 Students', Student::where('level_id', 6)->count()),
        ];
    }
}
