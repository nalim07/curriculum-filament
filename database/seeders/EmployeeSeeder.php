<?php

namespace Database\Seeders;

use App\Models;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = User::create([
            'username' => 'superadmin',
            'email' => 'superadmin@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('superadmin'),
            'status' => true,
        ]);
        Artisan::call('shield:super-admin', ['--user' => $superAdmin->id]);
        $superAdmin->givePermissionTo(['can_access_panel_admin', 'can_access_panel_curriculum', 'can_access_panel_admission', 'can_access_panel_teacher', 'can_access_panel_teacher_pg_kg',]);

        Employee::create([
            'user_id' => $superAdmin->id,
            'employee_status_id' => 1,
            'employee_unit_id' => 2,
            'employee_position_id' => 1,
            'join_date' => '2022-01-01',
            'resign_date' => null,
            'permanent_date' => '2023-01-01',

            'email' => 'superadmin@gmail.com',
            'employee_code' => 'K002',
            'fullname' => 'Super Admin',
            'nik' => '1234567890123412',
            'number_account' => '123456719',
            'number_fingerprint' => 123,

            'number_npwp' => '123456789',
            'name_npwp' => 'Teacher Bros',
            'number_bpjs_ketenagakerjaan' => '123456789',
            'iuran_bpjs_ketenagakerjaan' => 'Rp. 100000',
            'number_bpjs_yayasan' => '123456789',
            'number_bpjs_pribadi' => '123456789',

            'gender' => '1',
            'religion' => '1',
            'place_of_birth' => 'Jakarta',
            'date_of_birth' => '1990-01-01',
            'address' => 'Jl. Contoh No. 123',
            'address_now' => 'Jl. Contoh Sekarang No. 456',
            'city' => 'Jakarta',
            'postal_code' => 12345,
            'phone_number' => '081234567890',
            'email_school' => 'john.doe@school.com',
            'citizen' => 'Indonesia',
            'marital_status' => '1',
            'partner_name' => 'Jane Doe',
            'number_of_childern' => '2',
            'notes' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
        ]);

        $guru = User::create([
            'username' => 'guru',
            'email' => 'guru@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('123456'),
            'status' => true,
        ]);
        $guru->assignRole(['teacher']);
        $employee = Employee::create([
            'user_id' => $guru->id,
            'employee_status_id' => 1,
            'employee_unit_id' => 2,
            'employee_position_id' => 1,
            'join_date' => '2022-01-01',
            'resign_date' => null,
            'permanent_date' => '2023-01-01',

            'email' => 'guru@gmail.com',
            'employee_code' => '2307014',
            'fullname' => 'Teacher Bros',
            'nik' => '1234567890123456',
            'number_account' => '123456789',
            'number_fingerprint' => 123,

            'number_npwp' => '123456789',
            'name_npwp' => 'Teacher Bros',
            'number_bpjs_ketenagakerjaan' => '123456789',
            'iuran_bpjs_ketenagakerjaan' => 'Rp. 100000',
            'number_bpjs_yayasan' => '123456789',
            'number_bpjs_pribadi' => '123456789',

            'gender' => '1',
            'religion' => '1',
            'place_of_birth' => 'Jakarta',
            'date_of_birth' => '1990-01-01',
            'address' => 'Jl. Contoh No. 123',
            'address_now' => 'Jl. Contoh Sekarang No. 456',
            'city' => 'Jakarta',
            'postal_code' => 12345,
            'phone_number' => '081234567890',
            'email_school' => 'john.doe@school.com',
            'citizen' => 'Indonesia',
            'marital_status' => '1',
            'partner_name' => 'Jane Doe',
            'number_of_childern' => '2',
            'notes' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
        ]);
        Teacher::create([
            'employee_id' => $employee->id,
        ]);


        $guru = User::create([
            'username' => 'guru2',
            'email' => 'guru2@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('123456'),
            'status' => true,
        ]);
        $guru->assignRole(['teacher_pg_kg']);
        $employee = Employee::create([
            'user_id' => $guru->id,
            'employee_status_id' => 1,
            'employee_unit_id' => 2,
            'employee_position_id' => 1,
            'join_date' => '2022-01-01',
            'resign_date' => null,
            'permanent_date' => '2023-01-01',

            'email' => 'guru2@gmail.com',
            'employee_code' => 'K001212',
            'fullname' => 'Guru 2',
            'nik' => '1234567890123412',
            'number_account' => '123456719',
            'number_fingerprint' => 123,

            'number_npwp' => '123456789',
            'name_npwp' => 'Teacher Bros',
            'number_bpjs_ketenagakerjaan' => '123456789',
            'iuran_bpjs_ketenagakerjaan' => 'Rp. 100000',
            'number_bpjs_yayasan' => '123456789',
            'number_bpjs_pribadi' => '123456789',

            'gender' => '1',
            'religion' => '1',
            'place_of_birth' => 'Jakarta',
            'date_of_birth' => '1990-01-01',
            'address' => 'Jl. Contoh No. 123',
            'address_now' => 'Jl. Contoh Sekarang No. 456',
            'city' => 'Jakarta',
            'postal_code' => 12345,
            'phone_number' => '081234567890',
            'email_school' => 'john.doe@school.com',
            'citizen' => 'Indonesia',
            'marital_status' => '1',
            'partner_name' => 'Jane Doe',
            'number_of_childern' => '2',
            'notes' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
        ]);
        Teacher::create([
            'employee_id' => $employee->id,
        ]);

        $curriculum = User::create([
            'username' => 'curriculum',
            'email' => 'curriculum@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('123456'),
            'status' => true,
        ]);
        $curriculum->assignRole('curriculum');
        $employee = Employee::create([
            'user_id' => $curriculum->id,
            'employee_status_id' => 1,
            'employee_unit_id' => 2,
            'employee_position_id' => 1,
            'join_date' => '2022-01-01',
            'resign_date' => null,
            'permanent_date' => '2023-01-01',

            'email' => 'curriculum@gmail.com',
            'employee_code' => 'K001212',
            'fullname' => 'Curriculum',
            'nik' => '1234567890123412',
            'number_account' => '123456719',
            'number_fingerprint' => 123,

            'number_npwp' => '123456789',
            'name_npwp' => 'Teacher Bros',
            'number_bpjs_ketenagakerjaan' => '123456789',
            'iuran_bpjs_ketenagakerjaan' => 'Rp. 100000',
            'number_bpjs_yayasan' => '123456789',
            'number_bpjs_pribadi' => '123456789',

            'gender' => '1',
            'religion' => '1',
            'place_of_birth' => 'Jakarta',
            'date_of_birth' => '1990-01-01',
            'address' => 'Jl. Contoh No. 123',
            'address_now' => 'Jl. Contoh Sekarang No. 456',
            'city' => 'Jakarta',
            'postal_code' => 12345,
            'phone_number' => '081234567890',
            'email_school' => 'john.doe@school.com',
            'citizen' => 'Indonesia',
            'marital_status' => '1',
            'partner_name' => 'Jane Doe',
            'number_of_childern' => '2',
            'notes' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
        ]);
        Teacher::create([
            'employee_id' => $employee->id,
        ]);


        $tu = User::create([
            'username' => 'tu',
            'email' => 'tu@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('123456'),
            'status' => true,
        ]);
        $tu->assignRole('admission');
        Employee::create([
            'user_id' => $tu->id,
            'employee_status_id' => 1,
            'employee_unit_id' => 2,
            'employee_position_id' => 1,
            'join_date' => '2022-01-01',
            'resign_date' => null,
            'permanent_date' => '2023-01-01',

            'email' => 'tu@gmail.com',
            'employee_code' => 'K001213',
            'fullname' => 'Admission',
            'nik' => '1234567890123416',
            'number_account' => '123456718',
            'number_fingerprint' => 121,

            'number_npwp' => '123456789',
            'name_npwp' => 'Teacher Bros',
            'number_bpjs_ketenagakerjaan' => '123456789',
            'iuran_bpjs_ketenagakerjaan' => 'Rp. 100000',
            'number_bpjs_yayasan' => '123456789',
            'number_bpjs_pribadi' => '123456789',

            'gender' => '1',
            'religion' => '1',
            'place_of_birth' => 'Jakarta',
            'date_of_birth' => '1990-01-01',
            'address' => 'Jl. Contoh No. 123',
            'address_now' => 'Jl. Contoh Sekarang No. 456',
            'city' => 'Jakarta',
            'postal_code' => 12345,
            'phone_number' => '081234567890',
            'email_school' => 'john.doe@school.com',
            'citizen' => 'Indonesia',
            'marital_status' => '1',
            'partner_name' => 'Jane Doe',
            'number_of_childern' => '2',
            'notes' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
        ]);
    }
}
