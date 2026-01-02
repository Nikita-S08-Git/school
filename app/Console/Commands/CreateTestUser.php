<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Student;

class CreateTestUser extends Command
{
    protected $signature = 'test:create-users';
    protected $description = 'Create test users for compliance testing';

    public function handle()
    {
        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            ['name' => 'Admin User', 'password' => bcrypt('password123')]
        );
        $this->info("Admin created: {$admin->email}");

        // Create student_section user
        $studentSection = User::firstOrCreate(
            ['email' => 'student_section@test.com'],
            ['name' => 'Student Section User', 'password' => bcrypt('password123')]
        );
        $this->info("Student Section user created: {$studentSection->email}");

        // Create test student
        $student = Student::firstOrCreate(
            ['id' => 3],
            [
                'name' => 'Test Student',
                'email' => 'student@test.com',
                'roll_number' => '2025/BCOM/A/003',
                'program_id' => 1,
                'division_id' => 1,
                'academic_year' => '2024-25'
            ]
        );
        $this->info("Test student created: {$student->name}");

        return 0;
    }
}