<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DepartmentSeeder::class,
            ProgramSeeder::class,
            AcademicSessionSeeder::class,
            AcademicYearSeeder::class,
            DivisionSeeder::class,
            FeeHeadSeeder::class,
            FeeStructureSeeder::class,
            ScholarshipSeeder::class,
            StudentSeeder::class,
        ]);
    }
}