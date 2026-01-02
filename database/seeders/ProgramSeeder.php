<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Academic\Program;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        Program::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'Bachelor of Commerce',
                'short_name' => 'B.Com',
                'code' => 'BCOM',
                'department_id' => 1,
                'duration_years' => 3,
                'total_semesters' => 6,
                'program_type' => 'undergraduate',
                'is_active' => true,
            ]
        );

        Program::updateOrCreate(
            ['id' => 2],
            [
                'name' => 'Bachelor of Science',
                'short_name' => 'B.Sc',
                'code' => 'BSC',
                'department_id' => 2,
                'duration_years' => 3,
                'total_semesters' => 6,
                'program_type' => 'undergraduate',
                'is_active' => true,
            ]
        );
    }
}