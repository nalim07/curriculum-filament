<?php

namespace App\Jobs;

use App\Models\User;
use App\Helpers\Helper;
use App\Models\Student;
use App\Models\ClassSchool;
use App\Models\Level;
use App\Models\Line;
use App\Models\MemberClassSchool;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Str;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class ImportStudentsJob implements ShouldQueue
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
     */
    public function handle()
    {
        $this->data = $this->normalizeData($this->data);

        // Create or update the student record
        $student = Student::firstOrNew([
            'nis' => $this->data['nis'],
        ]);

        $user = User::firstOrNew([
            'username' => $this->data['nis'],
        ], [
            'email' => $this->data['email'],
            'status' => true,
            'password' => bcrypt($this->data['nis']),
        ]);

        // Ensure UUID is set if the user is new
        if (!$user->exists) {
            $user->id = (string) Str::uuid();
        }

        $user->assignRole('student');
        $user->save();

        $classSchool = ClassSchool::where('name', $this->data['class_school_id'])->first();
        $classSchoolId = $classSchool ? $classSchool->id : null;

        $level = Level::where('name', $this->data['level_id'])->value('id');
        $line = Line::where('name', $this->data['line_id'])->value('id');

        $studentData = [
            'user_id' => $user->id,
            'fullname' => $this->data['fullname'],
            'username' => $this->data['username'],
            'email' => $this->data['email'],
            'nis' => $this->data['nis'],
            'nisn' => $this->data['nisn'],
            'nik' => $this->data['nik'],
            'registration_type' => Helper::getRegistrationTypeByName($this->data['registration_type']) ?? '1',
            'entry_year' => $this->data['entry_year'],
            'entry_semester' => $this->data['entry_semester'],
            'entry_class' => $this->data['entry_class'],
            'class_school_id' => $classSchoolId,
            'level_id' => $level,
            'line_id' => $line,
            'gender' => Helper::getSexByName($this->data['gender']),
            'blood_type' => $this->data['blood_type'],
            'religion' => Helper::getReligionByName($this->data['religion']),
            'place_of_birth' => $this->data['place_of_birth'],
            'date_of_birth' => Helper::formatDate($this->data['date_of_birth']),
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
            'father_date_of_birth' => Helper::formatDate($this->data['father_date_of_birth']),
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
            'mother_date_of_birth' => Helper::formatDate($this->data['mother_date_of_birth']),
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
            'guardian_date_of_birth' => Helper::formatDate($this->data['guardian_date_of_birth']),
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
        ];

        $student = Student::updateOrCreate(
            ['nis' => $this->data['nis']],
            $studentData
        );

        if ($classSchoolId) {
            MemberClassSchool::updateOrCreate([
                'student_id' => $student->id,
                'class_school_id' => $classSchoolId,
                'academic_year_id' => $classSchool->academic_year_id,
                'registration_type' => Helper::getRegistrationTypeByName($this->data['registration_type']) ?? '1',
            ]);
        }
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
}
