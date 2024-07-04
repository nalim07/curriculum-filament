<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ['super_admin', 'admin', 'admission', 'curriculum', 'teacher', 'co_teacher', 'teacher_pg_kg', 'co_teacher_pg_kg', 'student'];

        // custom permissions
        $permissions = ['can_access_panel_admin', 'can_access_panel_curriculum', 'can_access_panel_admission', 'can_access_panel_teacher', 'can_access_panel_teacher_pg_kg', 'can_access_panel_student'];

        foreach ($permissions as $key => $permission) {
            DB::table('permissions')->insert([
                'name' => $permission,
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach ($roles as $key => $role) {
            DB::table('roles')->insert([
                'name' => $role,
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Define the role to permission mapping
        $rolePermissions = [
            'admin' => ['can_access_panel_admin'],
            'curriculum' => ['can_access_panel_curriculum'],
            'admission' => ['can_access_panel_admission'],
            'teacher' => ['can_access_panel_teacher'],
            'teacher_pg_kg' => ['can_access_panel_teacher_pg_kg'],
            'student' => ['can_access_panel_student'],
        ];

        // Assign permissions to roles
        foreach ($rolePermissions as $role => $permissions) {
            $roleId = DB::table('roles')->where('name', $role)->first()->id;

            foreach ($permissions as $permission) {
                $permissionId = DB::table('permissions')->where('name', $permission)->first()->id;

                DB::table('role_has_permissions')->insert([
                    'role_id' => $roleId,
                    'permission_id' => $permissionId,
                ]);
            }
        }

        // Add these lines to your permission seeding logic
        Permission::create(['name' => 'export_student']);
        Permission::create(['name' => 'import_student']);

        $role = Role::findByName('curriculum');
        $role->givePermissionTo('export_student');
        $role->givePermissionTo('import_student');

        $role = Role::findByName('super_admin');
        $role->givePermissionTo('export_student');
        $role->givePermissionTo('import_student');
    }
}
