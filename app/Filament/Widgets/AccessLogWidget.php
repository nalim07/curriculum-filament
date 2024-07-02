<?php

namespace App\Filament\Widgets;

use Filament\Widgets\TableWidget;
use Tables\Columns\DateTimeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables;  // Ensure Filament's table classes are imported correctly
use Spatie\Activitylog\Models\Activity;  // Confirm this matches your installed packages and configurations


class AccessLogWidget extends TableWidget
{
    protected static ?int $sort = 7;
    protected static ?string $heading = 'Access Log';

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Activity::query()->where('description', 'like', '%logged in%');
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('log_name')->label('Type'),
            TextColumn::make('description')->label('Description'),
            TextColumn::make('causer.name')->label('User'), // Asumsi relasi ke model User
            TextColumn::make('created_at')->label('Date'),
        ];
    }

    protected function getTableEmptyStateHeading(): string
    {
        return 'No access activities found.';
    }
}
