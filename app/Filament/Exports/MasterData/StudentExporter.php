<?php

namespace App\Filament\Exports\MasterData;

use App\Helpers\Helper;
use App\Models\Student;
use Filament\Actions\Exports\Exporter;
use Illuminate\Database\Eloquent\Model;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Common\Entity\Style\Border;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Models\Export;
use OpenSpout\Common\Entity\Style\BorderPart;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\CellVerticalAlignment;

class StudentExporter extends Exporter
{
    protected static ?string $model = Student::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('fullname'),
            ExportColumn::make('username'),
            ExportColumn::make('email'),
            ExportColumn::make('nis'),
            ExportColumn::make('nisn'),
            ExportColumn::make('nik'),
            ExportColumn::make('registration_type')
                ->formatStateUsing(fn (?string $state): string => Helper::getRegistrationType($state ?? '')),
            ExportColumn::make('entry_year'),
            ExportColumn::make('entry_semester'),
            ExportColumn::make('entry_class'),
            ExportColumn::make('class_school_id')
                ->label('Class School')
                ->formatStateUsing(fn (?Model $record): string => $record->classSchool?->name ?? ''),
            ExportColumn::make('level_id')
                ->label('Level')
                ->formatStateUsing(fn (?Model $record): string => $record->level?->name ?? ''),
            ExportColumn::make('line_id')
                ->label('Line')
                ->formatStateUsing(fn (?Model $record): string => $record->line?->name ?? ''),
            ExportColumn::make('gender')
                ->formatStateUsing(fn (?string $state): string => Helper::getSex($state ?? '')),
            ExportColumn::make('blood_type'),
            ExportColumn::make('religion')
                ->formatStateUsing(fn (?string $state): string => Helper::getReligion($state ?? '')),
            ExportColumn::make('blood_type'),
            ExportColumn::make('religion'),
            ExportColumn::make('place_of_birth'),
            ExportColumn::make('date_of_birth'),
            ExportColumn::make('anak_ke'),
            ExportColumn::make('number_of_sibling'),
            ExportColumn::make('citizen'),
            ExportColumn::make('address'),
            ExportColumn::make('city'),
            ExportColumn::make('postal_code'),
            ExportColumn::make('distance_home_to_school'),
            ExportColumn::make('email_parent'),
            ExportColumn::make('phone_number'),
            ExportColumn::make('living_together'),
            ExportColumn::make('transportation'),
            ExportColumn::make('nik_father'),
            ExportColumn::make('father_name'),
            ExportColumn::make('father_place_of_birth'),
            ExportColumn::make('father_date_of_birth'),
            ExportColumn::make('father_address'),
            ExportColumn::make('father_phone_number'),
            ExportColumn::make('father_religion')
                ->formatStateUsing(fn (?string $state): string => Helper::getReligion($state ?? '')),
            ExportColumn::make('father_city'),
            ExportColumn::make('father_last_education'),
            ExportColumn::make('father_job'),
            ExportColumn::make('father_income'),
            ExportColumn::make('nik_mother'),
            ExportColumn::make('mother_name'),
            ExportColumn::make('mother_place_of_birth'),
            ExportColumn::make('mother_date_of_birth'),
            ExportColumn::make('mother_address'),
            ExportColumn::make('mother_phone_number'),
            ExportColumn::make('mother_religion')
                ->formatStateUsing(fn (?string $state): string => Helper::getReligion($state ?? '')),
            ExportColumn::make('mother_city'),
            ExportColumn::make('mother_last_education'),
            ExportColumn::make('mother_job'),
            ExportColumn::make('mother_income'),
            ExportColumn::make('nik_guardian'),
            ExportColumn::make('guardian_name'),
            ExportColumn::make('guardian_place_of_birth'),
            ExportColumn::make('guardian_date_of_birth'),
            ExportColumn::make('guardian_address'),
            ExportColumn::make('guardian_phone_number'),
            ExportColumn::make('guardian_religion')
                ->formatStateUsing(fn (?string $state): string => Helper::getReligion($state ?? '')),
            ExportColumn::make('guardian_city'),
            ExportColumn::make('guardian_last_education'),
            ExportColumn::make('guardian_job'),
            ExportColumn::make('guardian_income'),
            ExportColumn::make('height'),
            ExportColumn::make('weight'),
            ExportColumn::make('special_treatment'),
            ExportColumn::make('note_health'),

            ExportColumn::make('old_school_entry_date'),
            ExportColumn::make('old_school_leaving_date'),
            ExportColumn::make('old_school_name'),
            ExportColumn::make('old_school_achivements'),
            ExportColumn::make('old_school_achivements_year'),
            ExportColumn::make('certificate_number_old_school'),
            ExportColumn::make('old_school_address'),
            ExportColumn::make('no_sttb'),
            ExportColumn::make('nem'),

            ExportColumn::make('photo'),
            ExportColumn::make('photo_document_health'),
            ExportColumn::make('photo_list_questions'),
            ExportColumn::make('photo_document_old_school'),

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
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER)
            ->setBorder(new Border(
                new BorderPart(Border::LEFT, Color::BLACK, Border::WIDTH_THIN),
                new BorderPart(Border::RIGHT, Color::BLACK, Border::WIDTH_THIN),
                new BorderPart(Border::TOP, Color::BLACK, Border::WIDTH_THIN),
                new BorderPart(Border::BOTTOM, Color::BLACK, Border::WIDTH_THIN)
            ));
    }

    public function getXlsxCellStyle(): ?Style
    {
        return (new Style())
            ->setFontSize(12)
            ->setFontName('Arial')
            ->setBorder(new Border(
                new BorderPart(Border::LEFT, Color::BLACK, Border::WIDTH_THIN),
                new BorderPart(Border::RIGHT, Color::BLACK, Border::WIDTH_THIN),
                new BorderPart(Border::TOP, Color::BLACK, Border::WIDTH_THIN),
                new BorderPart(Border::BOTTOM, Color::BLACK, Border::WIDTH_THIN)
            ));
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your student export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
