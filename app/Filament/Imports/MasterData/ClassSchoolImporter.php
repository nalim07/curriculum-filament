<?php

namespace App\Filament\Imports\MasterData;

use App\Models\Line;
use App\Models\Level;
use App\Models\Teacher;
use App\Models\Employee;
use App\Models\ClassSchool;
use App\Models\AcademicYear;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Models\Import;

class ClassSchoolImporter extends Importer
{
    protected static ?string $model = ClassSchool::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('level_id')
                ->label('level_id')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('line_id')
                ->label('line_id')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('academic_year_id')
                ->label('academic_year_id')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('teacher_id')
                ->label('teacher_id')
                ->helperText('make sure teacher is available in teacher list')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:30']),
        ];
    }

    protected function normalizeData(array $data): array
    {
        $normalizedData = [];
        $headerMap = [
            'level id' => 'level_id',
            'line id' => 'line_id',
            'academic year id' => 'academic_year_id',
            'teacher id' => 'teacher_id',
            'name' => 'name',
        ];

        foreach ($data as $key => $value) {
            $normalizedKey = strtolower(trim($key));
            if (isset($headerMap[$normalizedKey])) {
                $normalizedKey = $headerMap[$normalizedKey];
            }
            $normalizedData[$normalizedKey] = $value;
        }

        return $normalizedData;
    }
    public function resolveRecord(): ?ClassSchool
    {
        $this->data = $this->normalizeData($this->data);

        // Debugging output
        logger()->info('Normalized Data:', $this->data);

        // Get the academic year ID
        $academicYear = AcademicYear::where('year', $this->data['academic_year_id'])->first();
        $academicYearId = $academicYear ? $academicYear->id : null;

        // Get the level ID
        $level = Level::where('name', $this->data['level_id'])->first();
        $levelId = $level ? $level->id : null;

        // Get the line ID
        $line = Line::where('name', $this->data['line_id'])->first();
        $lineId = $line ? $line->id : null;

        // Get the teacher ID
        $employee = Employee::where('fullname', $this->data['teacher_id'])->first();
        $teacher = $employee ? Teacher::where('employee_id', $employee->id)->first() : null;
        $teacherId = $teacher ? $teacher->id : null;

        // Debugging output for resolved IDs
        logger()->info('Resolved IDs:', [
            'academic_year_id' => $academicYearId,
            'level_id' => $levelId,
            'line_id' => $lineId,
            'teacher_id' => $teacherId,
        ]);

        if (is_null($academicYearId) || is_null($levelId) || is_null($lineId) || is_null($teacherId)) {
            // If any of the required IDs are not found, handle the error here
            throw new \Exception('Required data not found or invalid');
        }

        // Find existing ClassSchool by academic_year_id, level_id, line_id, and name, or create new
        $classSchool = ClassSchool::firstOrNew([
            'academic_year_id' => $academicYearId,
            'level_id' => $levelId,
            'line_id' => $lineId,
            'name' => $this->data['name'],
        ]);

        // Update the classSchool attributes
        $classSchool->academic_year_id = $academicYearId;
        $classSchool->level_id = $levelId;
        $classSchool->line_id = $lineId;
        $classSchool->teacher_id = $teacherId;

        // Debugging output for the classSchool object
        logger()->info('ClassSchool Attributes before save:', $classSchool->getAttributes());

        // Save the classSchool record
        $classSchool->save();

        // Final debugging output
        logger()->info('Saved ClassSchool:', $classSchool->toArray());

        return $classSchool;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your class school import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
