<?php

namespace App\Filament\Imports;

use App\Helpers\Helper;
use App\Models\Employee;
use App\Models\EmployeeUnit;
use App\Models\EmployeeStatus;
use Illuminate\Support\Carbon;
use App\Jobs\ImportEmployeeJob;
use App\Jobs\ImportEmployeesJob;
use App\Models\EmployeePosition;
use Spatie\Permission\Models\Role;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Validation\ValidationException;

class EmployeeImporter extends Importer
{
    protected static ?string $model = Employee::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('fullname')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('employee_code')
                ->requiredMapping()
                ->rules(['required', 'max:25']),
            ImportColumn::make('roles')
                ->label('Roles')
                ->fillRecordUsing(function (Employee $record, ?string $state): void {
                    $roles = explode(',', $state);
                    $roles = array_map('trim', $roles);
                    $roleIds = Role::whereIn('name', $roles)->pluck('id')->toArray();
                    $record->user->roles()->sync($roleIds);
                })
                ->rules(['nullable']),
            ImportColumn::make('email')
                ->rules(['nullable']),
            ImportColumn::make('employee_status_id')
                ->label('Employee Status')
                ->fillRecordUsing(function (Employee $record, ?string $state): void {
                    $employeeStatus = EmployeeStatus::where('name', $state)->first();
                    $record->employee_status_id = $employeeStatus ? $employeeStatus->id : null;
                })
                ->rules(['required']),
            ImportColumn::make('employee_unit_id')
                ->label('Employee Unit')
                ->fillRecordUsing(function (Employee $record, ?string $state): void {
                    $employeeUnit = EmployeeUnit::where('name', $state)->first();
                    $record->employee_unit_id = $employeeUnit ? $employeeUnit->id : null;
                })
                ->rules(['required']),
            ImportColumn::make('employee_position_id')
                ->label('Employee Position')
                ->fillRecordUsing(function (Employee $record, ?string $state): void {
                    $employeePosition = EmployeePosition::where('name', $state)->first();
                    $record->employee_position_id = $employeePosition ? $employeePosition->id : null;
                })
                ->rules(['required']),

            ImportColumn::make('join_date')
                ->rules(['nullable'])
                ->fillRecordUsing(function (Employee $record, ?string $state): void {
                    $record->join_date = Helper::formatDate($state);
                }),
            ImportColumn::make('resign_date')
                ->rules(['nullable'])
                ->fillRecordUsing(function (Employee $record, ?string $state): void {
                    $record->resign_date = Helper::formatDate($state);
                }),
            ImportColumn::make('permanent_date')
                ->rules(['nullable'])
                ->fillRecordUsing(function (Employee $record, ?string $state): void {
                    $record->permanent_date = Helper::formatDate($state);
                }),
            ImportColumn::make('nik')
                ->rules(['nullable', 'max:16']),
            ImportColumn::make('number_account')
                ->rules(['max:255']),
            ImportColumn::make('number_fingerprint')
                ->rules(['max:255']),

            ImportColumn::make('number_npwp')
                ->rules(['max:255']),
            ImportColumn::make('name_npwp')
                ->rules(['max:255']),
            ImportColumn::make('number_bpjs_ketenagakerjaan')
                ->rules(['max:255']),
            ImportColumn::make('iuran_bpjs_ketenagakerjaan')
                ->rules(['max:255']),
            ImportColumn::make('number_bpjs_yayasan')
                ->rules(['max:255']),
            ImportColumn::make('number_bpjs_pribadi')
                ->rules(['max:255']),
            ImportColumn::make('gender')
                ->fillRecordUsing(function (Employee $record, ?string $state): void {
                    $record->gender = Helper::getSexByName($state);
                }),
            ImportColumn::make('religion')
                ->fillRecordUsing(function (Employee $record, ?string $state): void {
                    $record->religion = Helper::getReligionByName($state);
                }),
            ImportColumn::make('place_of_birth')
                ->rules(['max:50']),
            ImportColumn::make('date_of_birth')
                ->rules(['nullable'])
                ->fillRecordUsing(function (Employee $record, ?string $state): void {
                    $record->date_of_birth = Helper::formatDate($state);
                }),
            ImportColumn::make('address')
                ->rules(['max:255']),
            ImportColumn::make('address_now')
                ->rules(['max:255']),
            ImportColumn::make('city')
                ->rules(['max:255']),
            ImportColumn::make('postal_code')
                ->rules(['max:255']),
            ImportColumn::make('phone_number')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('email_school')
                ->rules(['nullable']),
            ImportColumn::make('citizen')
                ->rules(['max:255']),
            ImportColumn::make('marital_status')
                ->fillRecordUsing(function (Employee $record, ?string $state): void {
                    $record->marital_status = Helper::getMaritalStatusByName($state);
                }),
            ImportColumn::make('partner_name')
                ->rules(['max:255']),
            ImportColumn::make('number_of_childern')
                ->rules(['max:255']),
            ImportColumn::make('notes')
                ->rules(['max:255']),

            ImportColumn::make('photo')
                ->rules(['nullable']),
            ImportColumn::make('signature')
                ->rules(['nullable']),
            ImportColumn::make('photo_ktp')
                ->rules(['nullable']),
            ImportColumn::make('photo_npwp')
                ->rules(['nullable']),
            ImportColumn::make('photo_kk')
                ->rules(['nullable']),
            ImportColumn::make('other_document')
                ->rules(['nullable']),
        ];
    }

    protected function normalizeData(array $data): array
    {
        $normalizedData = [];
        $headerMap = [
            'employee_status' => 'employee_status_id',
            'employee_unit' => 'employee_unit_id',
            'employee_position' => 'employee_position_id',
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

    public function resolveRecord(): ?Employee
    {
        ImportEmployeesJob::dispatch($this->data);
        return null;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your employee import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
