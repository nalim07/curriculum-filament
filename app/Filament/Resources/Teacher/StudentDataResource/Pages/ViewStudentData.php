<?php

namespace App\Filament\Resources\Teacher\StudentDataResource\Pages;

use App\Filament\Resources\Teacher\StudentDataResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewStudentData extends ViewRecord
{
    protected static string $resource = StudentDataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\EditAction::make(),
        ];
    }
}
