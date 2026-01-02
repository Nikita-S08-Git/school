<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User\Student;
use App\Models\Fee\StudentFee;
use App\Models\User;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        // Create users first, then students
        for ($i = 1; $i <= 15; $i++) {
            $user = User::updateOrCreate(
                ['id' => $i + 10], // Start from ID 11 to avoid conflicts
                [
                    'name' => "Student User {$i}",
                    'email' => "student{$i}@test.com",
                    'password' => bcrypt('password123'),
                ]
            );

            // Determine program and division based on student number
            if ($i <= 5) {
                $programId = 1; // BCOM
                $divisionId = 1; // A
                $rollNumber = sprintf('2025/BCOM/A/%03d', $i);
                $name = "BCOM Student A{$i}";
            } elseif ($i <= 10) {
                $programId = 1; // BCOM
                $divisionId = 2; // B
                $rollNumber = sprintf('2025/BCOM/B/%03d', $i - 5);
                $name = "BCOM Student B" . ($i - 5);
            } else {
                $programId = 2; // BSC
                $divisionId = 4; // A (BSC A is division ID 4)
                $rollNumber = sprintf('2025/BSC/A/%03d', $i - 10);
                $name = "BSC Student A" . ($i - 10);
            }

            Student::updateOrCreate(
                ['id' => $i],
                [
                    'user_id' => $user->id,
                    'admission_number' => "ADM2024" . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'roll_number' => $rollNumber,
                    'first_name' => explode(' ', $name)[0],
                    'last_name' => 'Student',
                    'date_of_birth' => '2005-01-01',
                    'gender' => 'male',
                    'program_id' => $programId,
                    'academic_year' => '2024-25',
                    'division_id' => $divisionId,
                    'academic_session_id' => 1, // Assuming session ID 1 exists
                    'admission_date' => '2024-07-01',
                    'student_status' => 'active',
                ]
            );
        }
    }
}