<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\StudentController;
use App\Http\Controllers\Web\AdmissionController;

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Public admission form
Route::get('/apply', [AdmissionController::class, 'showApplyForm'])->name('admissions.apply.form');
Route::post('/apply', [AdmissionController::class, 'apply'])->name('admissions.apply');

// Root route - redirect to login if not authenticated
Route::get('/', function() {
    if (auth()->check()) {
        $role = auth()->user()->roles->first()->name ?? 'student';
        return redirect()->route("dashboard.{$role}");
    }
    return redirect()->route('login');
});

// Protected Routes
Route::middleware(['auth'])->group(function () {
    
    Route::get('/dashboard/principal', [DashboardController::class, 'principal'])->name('dashboard.principal');
    Route::get('/dashboard/student', [DashboardController::class, 'student'])->name('dashboard.student');
    Route::get('/dashboard/teacher', [DashboardController::class, 'teacher'])->name('dashboard.teacher');
    Route::get('/dashboard/office', [DashboardController::class, 'office'])->name('dashboard.office');
    Route::get('/dashboard/accounts_staff', [DashboardController::class, 'accounts_staff'])->name('dashboard.accounts_staff');
    Route::get('/dashboard/librarian', [DashboardController::class, 'librarian'])->name('dashboard.librarian');
    
    // Student Management
    Route::resource('students', StudentController::class);
    Route::get('/dashboard/students', [StudentController::class, 'index'])->name('dashboard.students');
    
    // Admission Management
    Route::get('/admissions', [AdmissionController::class, 'index'])->name('admissions.index');
    Route::get('/admissions/{admission}', [AdmissionController::class, 'show'])->name('admissions.show');
    Route::post('/admissions/{admission}/verify', [AdmissionController::class, 'verify'])->name('admissions.verify');
    Route::post('/admissions/{admission}/reject', [AdmissionController::class, 'reject'])->name('admissions.reject');
});