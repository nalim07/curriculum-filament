<?php

namespace App\Filament\Exports\MasterData;

use App\Models\ClassSchool;
use Filament\Actions\Exports\Exporter;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Models\Export;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\CellVerticalAlignment;

class ClassSchoolExporter extends Exporter
{
    protected static ?string $model = ClassSchool::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id'),
            ExportColumn::make('level_id')
                ->label('level_id')
                ->formatStateUsing(function (ClassSchool $classSchool) {
                    return $classSchool->level->name;
                }),
            ExportColumn::make('line_id')
                ->label('line_id')
                ->formatStateUsing(function (ClassSchool $classSchool) {
                    return $classSchool->line->name;
                }),
            ExportColumn::make('academic_year_id')
                ->label('academic_year_id')
                ->formatStateUsing(function (ClassSchool $classSchool) {
                    return $classSchool->academicYear->year;
                }),
            ExportColumn::make('teacher_id')
                ->label('teacher_id')
                ->formatStateUsing(function (ClassSchool $classSchool) {
                    return $classSchool->teacher->employee->fullname;
                }),
            ExportColumn::make('name'),
        ];
    }

    public function getXlsxHeaderCellStyle(): ?Style
    {
        return (new Style())
            ->setFontBold()
            ->setFontSize(12)
            ->setFontName('Arial')
            ->setFontColor(Color::BLACK)
            ->setCellAlignment(CellAlignment::CENTER)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER);
    }

    public function getXlsxCellStyle(): ?Style
    {
        return (new Style())
            ->setFontSize(12)
            ->setFontName('Arial');
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your class school export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
