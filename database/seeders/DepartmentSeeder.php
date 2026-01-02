<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Academic\Department;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        Department::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'Commerce',
                'code' => 'COM',
                'description' => 'Department of Commerce and Management',
                'is_active' => true,
            ]
        );

        Department::updateOrCreate(
            ['id' => 2],
            [
                'name' => 'Science',
                'code' => 'SCI',
                'description' => 'Department of Science and Technology',
                'is_active' => true,
            ]
        );
    }
}