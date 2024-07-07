<?php

namespace App\Jobs;

use App\Models\User;
use App\Helpers\Helper;
use App\Models\Teacher;
use App\Models\Employee;
use App\Models\EmployeeUnit;
use Illuminate\Bus\Queueable;
use App\Models\EmployeeStatus;
use App\Models\EmployeePosition;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Validation\ValidationException;

class ImportEmployeesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    /**
     * Create a new job instance.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws ValidationException
     */
    public function handle(): void
    {
        $this->data = $this->normalizeData($this->data);

        // Create or update the employee record
        $employee = Employee::firstOrNew([
            'employee_code' => $this->data['employee_code'],
        ]);

        // Create or update the user record
        $user = User::firstOrNew([
            'username' => $this->data['employee_code'],
            'email' => $this->data['email'],
            'status' => true
        ]);
        $user->password = bcrypt($this->data['employee_code']);
        $user->save();

        // Assign roles to user
        if (isset($this->data['roles'])) {
            $roleNames = explode(',', $this->data['roles']);
            $roleNames = array_map('trim', $roleNames);
            $user->assignRole($roleNames);
        }

        // Resolve related entities
        $employeeStatus = EmployeeStatus::where('name', $this->data['employee_status_id'])->value('id');
        $employeeUnit = EmployeeUnit::where('name', $this->data['employee_unit_id'])->value('id');
        $employeePosition = EmployeePosition::where('name', $this->data['employee_position_id'])->value('id');

        // Update the employee attributes
        $employee->fill([
            'user_id' => $user->id,
            'fullname' => $this->data['fullname'],
            'email' => $this->data['email'],
            'employee_status_id' => $employeeStatus,
            'employee_unit_id' => $employeeUnit,
            'employee_position_id' => $employeePosition,
            'join_date' => Helper::formatDate($this->data['join_date']),
            'resign_date' => Helper::formatDate($this->data['resign_date']),
            'permanent_date' => Helper::formatDate($this->data['permanent_date']),
            'nik' => $this->data['nik'],
            'number_account' => $this->data['number_account'],
            'number_fingerprint' => $this->data['number_fingerprint'],
            'number_npwp' => $this->data['number_npwp'],
            'name_npwp' => $this->data['name_npwp'],
            'number_bpjs_ketenagakerjaan' => $this->data['number_bpjs_ketenagakerjaan'],
            'iuran_bpjs_ketenagakerjaan' => $this->data['iuran_bpjs_ketenagakerjaan'],
            'number_bpjs_yayasan' => $this->data['number_bpjs_yayasan'],
            'number_bpjs_pribadi' => $this->data['number_bpjs_pribadi'],
            'gender' => Helper::getSexByName($this->data['gender']),
            'religion' => Helper::getReligionByName($this->data['religion']),
            'place_of_birth' => $this->data['place_of_birth'],
            'date_of_birth' => Helper::formatDate($this->data['date_of_birth']),
            'address' => $this->data['address'],
            'address_now' => $this->data['address_now'],
            'city' => $this->data['city'],
            'postal_code' => $this->data['postal_code'],
            'phone_number' => $this->data['phone_number'],
            'email_school' => $this->data['email_school'],
            'citizen' => $this->data['citizen'],
            'marital_status' => Helper::getMaritalStatusByName($this->data['marital_status']),
            'partner_name' => $this->data['partner_name'],
            'number_of_childern' => $this->data['number_of_childern'],
            'notes' => $this->data['notes'],
            'photo' => $this->data['photo'],
            'signature' => $this->data['signature'],
            'photo_ktp' => $this->data['photo_ktp'],
            'photo_npwp' => $this->data['photo_npwp'],
            'photo_kk' => $this->data['photo_kk'],
            'other_document' => $this->data['other_document'],
        ]);

        $employee->save();

        // Check roles and create/update Teacher record
        $roles = explode(',', $this->data['roles']);
        $teacherRoles = ['teacher', 'teacher_pg_kg', 'co_teacher', 'co_teacher_pg_kg', 'curriculum'];

        Log::info('Checking roles for Teacher record', ['roles' => $roles]);

        if (array_intersect($roles, $teacherRoles)) {
            Log::info('Role matches found. Creating or updating Teacher record.', ['employee_id' => $employee->id]);

            Teacher::updateOrCreate(
                ['employee_id' => $employee->id],
                ['employee_id' => $employee->id]
            );
        }
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
}
