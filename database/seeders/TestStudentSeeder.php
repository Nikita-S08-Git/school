<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User\Student;

class TestStudentSeeder extends Seeder
{
    public function run()
    {
        Student::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'roll_number' => '001',
            'admission_number' => 'ADM001',
            'student_status' => 'active',
            'admission_date' => now(),
        ]);

        Student::create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane.smith@example.com',
            'roll_number' => '002',
            'admission_number' => 'ADM002',
            'student_status' => 'active',
            'admission_date' => now(),
        ]);

        Student::create([
            'first_name' => 'Mike',
            'last_name' => 'Johnson',
            'email' => 'mike.johnson@example.com',
            'roll_number' => '003',
            'admission_number' => 'ADM003',
            'student_status' => 'active',
            'admission_date' => now(),
        ]);
    }
}