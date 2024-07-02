<?php

namespace App\Filament\Imports\MasterData;

use App\Models\User;
use App\Helpers\Helper;
use App\Models\Line;
use App\Models\Level;
use App\Models\Student;
use App\Models\ClassSchool;
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
                ->rules(['required', 'email']),
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
                ->rules(['nullable', 'email']),
            ImportColumn::make('phone_number')
                ->rules(['nullable', 'max:13']),
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
                ->rules(['nullable', 'max:13']),
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
                ->rules(['nullable', 'max:13']),
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
                ->rules(['nullable', 'max:13']),
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
        $this->data = $this->normalizeData($this->data);

        // Create or update the student record
        $student = Student::firstOrNew([
            'nis' => $this->data['nis'],
        ]);

        // user
        $user = User::firstOrNew([
            'username' => $this->data['nis'],
            'email' => $this->data['email'],
            'status' => true
        ]);
        $user->password = bcrypt($this->data['nis']);
        $user->save();

        // class_school_id
        $classSchool = ClassSchool::where('name', $this->data['class_school_id'])->value('id');
        $level = Level::where('name', $this->data['level_id'])->value('id');
        $line = Line::where('name', $this->data['line_id'])->value('id');

        // Update the student attributes
        $student->fill([
            'user_id' => $user->id,
            'fullname' => $this->data['fullname'],
            'username' => $this->data['username'],
            'email' => $this->data['email'],
            'nisn' => $this->data['nisn'],
            'nik' => $this->data['nik'],
            'registration_type' => Helper::getRegistrationTypeByName($this->data['registration_type']),
            'entry_year' => $this->data['entry_year'],
            'entry_semester' => $this->data['entry_semester'],
            'entry_class' => $this->data['entry_class'],
            'class_school_id' => $classSchool,
            'level_id' => $level,
            'line_id' => $line,
            'gender' => Helper::getSexByName($this->data['gender']),
            'blood_type' => $this->data['blood_type'],
            'religion' => Helper::getReligionByName($this->data['religion']),
            'place_of_birth' => $this->data['place_of_birth'],
            'date_of_birth' => $this->data['date_of_birth'],
            'anak_ke' => $this->data['anak_ke'],
            'number_of_sibling' => $this->data['number_of_sibling'],
            'citizen' => $this->data['citizen'],
            'address' => $this->data['address'],
            'city' => $this->data['city'],
            'postal_code' => $this->data['postal_code'],
            'distance_home_to_school' => $this->data['distance_home_to_school'],
            'email_parent' => $this->data['email_parent'],
            'phone_number' => $this->data['phone_number'],
            'living_together' => $this->data['living_together'],
            'transportation' => $this->data['transportation'],
            'nik_father' => $this->data['nik_father'],
            'father_name' => $this->data['father_name'],
            'father_place_of_birth' => $this->data['father_place_of_birth'],
            'father_date_of_birth' => $this->data['father_date_of_birth'],
            'father_address' => $this->data['father_address'],
            'father_phone_number' => $this->data['father_phone_number'],
            'father_religion' => Helper::getReligionByName($this->data['father_religion']),
            'father_city' => $this->data['father_city'],
            'father_last_education' => $this->data['father_last_education'],
            'father_job' => $this->data['father_job'],
            'father_income' => $this->data['father_income'],
            'nik_mother' => $this->data['nik_mother'],
            'mother_name' => $this->data['mother_name'],
            'mother_place_of_birth' => $this->data['mother_place_of_birth'],
            'mother_date_of_birth' => $this->data['mother_date_of_birth'],
            'mother_address' => $this->data['mother_address'],
            'mother_phone_number' => $this->data['mother_phone_number'],
            'mother_religion' => Helper::getReligionByName($this->data['mother_religion']),
            'mother_city' => $this->data['mother_city'],
            'mother_last_education' => $this->data['mother_last_education'],
            'mother_job' => $this->data['mother_job'],
            'mother_income' => $this->data['mother_income'],
            'nik_guardian' => $this->data['nik_guardian'],
            'guardian_name' => $this->data['guardian_name'],
            'guardian_place_of_birth' => $this->data['guardian_place_of_birth'],
            'guardian_date_of_birth' => $this->data['guardian_date_of_birth'],
            'guardian_address' => $this->data['guardian_address'],
            'guardian_phone_number' => $this->data['guardian_phone_number'],
            'guardian_religion' => Helper::getReligionByName($this->data['guardian_religion']),
            'guardian_city' => $this->data['guardian_city'],
            'guardian_last_education' => $this->data['guardian_last_education'],
            'guardian_job' => $this->data['guardian_job'],
            'guardian_income' => $this->data['guardian_income'],
            'height' => $this->data['height'],
            'weight' => $this->data['weight'],
            'special_treatment' => $this->data['special_treatment'],
            'note_health' => $this->data['note_health'],

            'old_school_name' => $this->data['old_school_name'],
            'old_school_achivements' => $this->data['old_school_achivements'],
            'old_school_achivements_year' => $this->data['old_school_achivements_year'],
            'certificate_number_old_school' => $this->data['certificate_number_old_school'],
            'old_school_address' => $this->data['old_school_address'],
            'no_sttb' => $this->data['no_sttb'],
            'nem' => $this->data['nem'],
        ]);

        // Save the student record
        $student->save();

        return $student;
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
