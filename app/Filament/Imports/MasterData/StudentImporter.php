<?php

namespace App\Filament\Imports\MasterData;

use Carbon\Carbon;
use App\Models\Line;
use App\Models\User;
use App\Models\Level;
use App\Helpers\Helper;
use App\Models\Student;
use App\Models\ClassSchool;
use Illuminate\Support\Str;
use App\Jobs\ImportStudentJob;
use App\Jobs\ImportStudentsJob;
use App\Models\MemberClassSchool;
use Illuminate\Support\Facades\DB;
use Filament\Actions\Imports\Importer;
use Illuminate\Database\Eloquent\Model;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Validation\ValidationException;

class StudentImporter extends Importer
{
    protected static ?string $model = Student::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('fullname')
                ->requiredMapping()
                ->rules(['required', 'max:100']),
            ImportColumn::make('username')
                ->requiredMapping()
                ->rules(['required', 'max:100']),
            ImportColumn::make('email')
                ->requiredMapping()
                ->rules(['nullable']),
            ImportColumn::make('nis')
                ->requiredMapping()
                ->rules(['required', 'max:10']),
            ImportColumn::make('nisn')
                ->rules(['nullable', 'max:10']),
            ImportColumn::make('nik')
                ->rules(['nullable', 'max:16']),
            ImportColumn::make('registration_type')
                ->rules(['required'])
                ->fillRecordUsing(fn (Student $record, ?string $state)
                => $record->registration_type = Helper::getRegistrationTypeByName($state)),
            ImportColumn::make('entry_year')
                ->rules(['nullable']),
            ImportColumn::make('entry_semester')
                ->rules(['nullable']),
            ImportColumn::make('entry_class')
                ->rules(['nullable']),
            ImportColumn::make('class_school_id')
                ->label('Class School')
                ->fillRecordUsing(function (Student $record, ?string $state): void {
                    $class = ClassSchool::where('name', $state)->where('academic_year_id', Helper::getActiveAcademicYearId())->first();
                    $record->class_school_id = $class ? $class->id : null;
                })
                ->rules(['nullable']),
            ImportColumn::make('level_id')
                ->label('Level')
                ->fillRecordUsing(function (Student $record, ?string $state): void {
                    $level = Level::where('name', $state)->first();
                    $record->level_id = $level ? $level->id : null;
                })
                ->rules(['nullable']),
            ImportColumn::make('line_id')
                ->label('Line')
                ->fillRecordUsing(function (Student $record, ?string $state): void {
                    $line = Line::where('name', $state)->first();
                    $record->line_id = $line ? $line->id : null;
                })
                ->rules(['nullable']),
            ImportColumn::make('gender')
                ->fillRecordUsing(fn (Student $record, ?string $state) => $record->gender = Helper::getSexByName($state))
                ->rules(['nullable']),
            ImportColumn::make('blood_type')
                ->rules(['nullable']),
            ImportColumn::make('religion')
                ->fillRecordUsing(fn (Student $record, ?string $state) => $record->religion = Helper::getReligionByName($state))
                ->rules(['nullable']),
            ImportColumn::make('place_of_birth')
                ->rules(['nullable', 'max:50']),
            ImportColumn::make('date_of_birth')
                ->fillRecordUsing(function (Student $record, ?string $state): void {
                    $record->date_of_birth = Helper::formatDate($state);
                })
                ->rules(['nullable', 'date_format:Y-m-d']),
            ImportColumn::make('anak_ke')
                ->rules(['nullable', 'max:2']),
            ImportColumn::make('number_of_sibling')
                ->rules(['nullable', 'max:2']),
            ImportColumn::make('citizen')
                ->rules(['nullable']),
            ImportColumn::make('address')
                ->rules(['nullable']),
            ImportColumn::make('city')
                ->rules(['nullable']),
            ImportColumn::make('postal_code')
                ->rules(['nullable']),
            ImportColumn::make('distance_home_to_school')
                ->rules(['nullable']),
            ImportColumn::make('email_parent')
                ->rules(['nullable']),
            ImportColumn::make('phone_number')
                ->rules(['nullable']),
            ImportColumn::make('living_together')
                ->rules(['nullable']),
            ImportColumn::make('transportation')
                ->rules(['nullable']),
            ImportColumn::make('nik_father')
                ->rules(['nullable', 'max:16']),
            ImportColumn::make('father_name')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('father_place_of_birth')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('father_date_of_birth')
                ->fillRecordUsing(function (Student $record, ?string $state): void {
                    $record->father_date_of_birth = Helper::formatDate($state);
                })
                ->rules(['nullable', 'date_format:Y-m-d']),
            ImportColumn::make('father_address')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('father_phone_number')
                ->rules(['nullable']),
            ImportColumn::make('father_religion')
                ->fillRecordUsing(fn (Student $record, ?string $state) => $record->father_religion = Helper::getReligionByName($state))
                ->rules(['nullable']),
            ImportColumn::make('father_city')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('father_last_education')
                ->rules(['nullable', 'max:25']),
            ImportColumn::make('father_job')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('father_income')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('nik_mother')
                ->rules(['nullable', 'max:16']),
            ImportColumn::make('mother_name')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('mother_place_of_birth')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('mother_date_of_birth')
                ->fillRecordUsing(function (Student $record, ?string $state): void {
                    $record->mother_date_of_birth = Helper::formatDate($state);
                })
                ->rules(['nullable', 'date_format:Y-m-d']),
            ImportColumn::make('mother_address')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('mother_phone_number')
                ->rules(['nullable']),
            ImportColumn::make('mother_religion')
                ->fillRecordUsing(fn (Student $record, ?string $state) => $record->mother_religion = Helper::getReligionByName($state))
                ->rules(['nullable']),
            ImportColumn::make('mother_city')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('mother_last_education')
                ->rules(['nullable', 'max:25']),
            ImportColumn::make('mother_job')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('mother_income')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('nik_guardian')
                ->rules(['nullable', 'max:16']),
            ImportColumn::make('guardian_name')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('guardian_place_of_birth')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('guardian_date_of_birth')
                ->fillRecordUsing(function (Student $record, ?string $state): void {
                    $record->guardian_date_of_birth = Helper::formatDate($state);
                })
                ->rules(['nullable', 'date_format:Y-m-d']),
            ImportColumn::make('guardian_address')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('guardian_phone_number')
                ->rules(['nullable']),
            ImportColumn::make('guardian_religion')
                ->fillRecordUsing(fn (Student $record, ?string $state) => $record->guardian_religion = Helper::getReligionByName($state))
                ->rules(['nullable']),
            ImportColumn::make('guardian_city')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('guardian_last_education')
                ->rules(['nullable', 'max:25']),
            ImportColumn::make('guardian_job')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('guardian_income')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('height')
                ->rules(['nullable']),
            ImportColumn::make('weight')
                ->rules(['nullable']),
            ImportColumn::make('special_treatment')
                ->rules(['nullable']),
            ImportColumn::make('note_health')
                ->rules(['nullable']),
            ImportColumn::make('old_school_entry_date')
                ->rules(['nullable']),
            ImportColumn::make('old_school_leaving_date')
                ->rules(['nullable']),
            ImportColumn::make('old_school_name')
                ->rules(['nullable']),
            ImportColumn::make('old_school_achivements')
                ->rules(['nullable']),
            ImportColumn::make('old_school_achivements_year')
                ->rules(['nullable']),
            ImportColumn::make('certificate_number_old_school')
                ->rules(['nullable']),
            ImportColumn::make('old_school_address')
                ->rules(['nullable']),
            ImportColumn::make('no_sttb')
                ->rules(['nullable']),
            ImportColumn::make('nem')
                ->rules(['nullable']),

            ImportColumn::make('photo')
                ->rules(['nullable']),
            ImportColumn::make('photo_document_health')
                ->rules(['nullable']),
            ImportColumn::make('photo_list_questions')
                ->rules(['nullable']),
            ImportColumn::make('photo_document_old_school')
                ->rules(['nullable']),
        ];
    }

    protected function normalizeData(array $data): array
    {
        $normalizedData = [];
        $headerMap = [
            'class_school' => 'class_school_id',
            'level' => 'level_id',
            'line' => 'line_id',
        ];

        foreach ($data as $key => $value) {
            $normalizedKey = strtolower(trim(str_replace(' ', '_', $key)));
            if (isset($headerMap[$normalizedKey])) {
                $normalizedKey = $headerMap[$normalizedKey];
            }
            $normalizedData[$normalizedKey] = $value;
        }

        return $normalizedData;
    }

    public function resolveRecord(): ?Student
    {
        ImportStudentsJob::dispatch($this->data);
        return null;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your student import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
