<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdmissionRoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions
        $permissions = [
            'view_admissions',
            'create_admissions',
            'verify_admissions',
            'verify_documents',
            'enroll_students',
            'reject_admissions'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create or update roles
        $studentSectionRole = Role::firstOrCreate(['name' => 'student_section']);
        $studentSectionRole->syncPermissions([
            'view_admissions',
            'verify_admissions', 
            'verify_documents',
            'enroll_students',
            'reject_admissions'
        ]);

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(Permission::all());

        $principalRole = Role::firstOrCreate(['name' => 'principal']);
        $principalRole->syncPermissions(['view_admissions']);

        $studentRole = Role::firstOrCreate(['name' => 'student']);
        $studentRole->syncPermissions(['create_admissions']);
    }
}