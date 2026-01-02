<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use App\Models\Student;
use App\Models\User;

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Create test student
try {
    $student = Student::create([
        'name' => 'Test Student',
        'email' => 'student@test.com',
        'roll_number' => '2025/BCOM/A/003',
        'program_id' => 1,
        'division_id' => 1,
        'academic_year' => '2024-25'
    ]);
    echo "Test student created with ID: {$student->id}\n";
} catch (Exception $e) {
    echo "Error creating student: " . $e->getMessage() . "\n";
    // Try to find existing student
    $student = Student::first();
    if ($student) {
        echo "Using existing student ID: {$student->id}\n";
    } else {
        echo "No students found in database\n";
        exit(1);
    }
}