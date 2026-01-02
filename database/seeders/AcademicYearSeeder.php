<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Academic\AcademicYear;

class AcademicYearSeeder extends Seeder
{
    public function run(): void
    {
        // BCOM Academic Years
        AcademicYear::updateOrCreate(
            ['id' => 1],
            [
                'program_id' => 1,
                'year_number' => 1,
                'year_name' => 'FY',
                'semester_start' => 1,
                'semester_end' => 2,
                'is_active' => true,
            ]
        );

        // BSC Academic Years
        AcademicYear::updateOrCreate(
            ['id' => 2],
            [
                'program_id' => 2,
                'year_number' => 1,
                'year_name' => 'FY',
                'semester_start' => 1,
                'semester_end' => 2,
                'is_active' => true,
            ]
        );
    }
}