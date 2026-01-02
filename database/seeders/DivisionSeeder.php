<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Academic\Division;

class DivisionSeeder extends Seeder
{
    public function run(): void
    {
        $divisions = ['A', 'B', 'C'];
        $academicYears = [1, 2]; // BCOM FY, BSC FY

        $id = 1;
        foreach ($academicYears as $academicYearId) {
            foreach ($divisions as $divisionName) {
                Division::updateOrCreate(
                    ['id' => $id],
                    [
                        'academic_year_id' => $academicYearId,
                        'division_name' => $divisionName,
                        'max_students' => 60,
                        'is_active' => true,
                    ]
                );
                $id++;
            }
        }
    }
}