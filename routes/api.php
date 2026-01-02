<?php

/**
 * =============================================================================
 * SCHOOL ERP API ROUTES - Main Entry Point for All API Endpoints
 * =============================================================================
 * 
 * This file defines ALL API routes for the School ERP system.
 * Think of this as a "menu" that tells the system which URL goes to which function.
 * 
 * HOW IT WORKS:
 * 1. When someone visits /api/students, Laravel looks here to find the right controller
 * 2. Routes are organized by functionality (students, fees, attendance, etc.)
 * 3. Most routes require authentication (login) except login itself
 * 
 * DATABASE CONNECTIONS:
 * - All routes connect to the main PostgreSQL database
 * - Each controller handles specific database tables
 * - Authentication uses 'users' table
 * 
 * FOR INTERNS: 
 * - Route::post() = Create new data (like adding a student)
 * - Route::get() = Read/fetch data (like getting student list)
 * - Route::put() = Update existing data (like editing student info)
 * - Route::delete() = Remove data (like deleting a student)
 * =============================================================================
 */

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Academic\StudentController;
use App\Http\Controllers\Api\Academic\DivisionController;
use App\Http\Controllers\Api\Fee\FeeController;
use App\Http\Controllers\Api\Attendance\AttendanceController;
use App\Http\Controllers\Api\Academic\DepartmentController;
use App\Http\Controllers\Api\Academic\ProgramController;
use App\Http\Controllers\Api\Academic\SubjectController;
use App\Http\Controllers\Api\Academic\AdmissionController;

/*
|--------------------------------------------------------------------------
| API Routes Configuration
|--------------------------------------------------------------------------
| All routes here are prefixed with /api/ automatically by Laravel
| Example: Route::post('/login') becomes /api/login in the browser
*/

// ========================================
// AUTHENTICATION ROUTES (No login required)
// ========================================
// These routes handle user login/logout and don't need authentication
// Database Tables Used: users, personal_access_tokens

// Test endpoint to verify API connectivity
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is working!',
        'timestamp' => now(),
        'server' => 'Laravel ' . app()->version()
    ]);
});

Route::post('/login', [AuthController::class, 'login']);           // POST /api/login - User login
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');  // POST /api/logout - User logout
Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');       // GET /api/user - Get current user info

// ========================================
// PROTECTED ROUTES (Login required for all below)
// ========================================
// All routes inside this group require valid authentication token
// If user is not logged in, they get "401 Unauthorized" error


 
Route::middleware(['auth:sanctum'])->group(function () {
    
    // ========================================
    // DEPARTMENT MANAGEMENT
    // ========================================
    // Handles Commerce, Science, Arts departments
    // Database Table: departments
    // Creates all CRUD routes: GET, POST, PUT, DELETE /api/departments
    Route::apiResource('departments', \App\Http\Controllers\Api\Academic\DepartmentController::class);


    // ========================================
    // PROGRAM MANAGEMENT  
    // ========================================
    // Handles degree programs like B.Com, B.Sc, MBA, etc.
    // Database Table: programs
    // Creates routes: GET, POST, PUT, DELETE /api/programs
   Route::apiResource('programs', \App\Http\Controllers\Api\ProgramController::class);
    
    // ========================================
    // SUBJECT MANAGEMENT
    // ========================================
    // Handles academic subjects for programs
    // Database Table: subjects
    // Creates routes: GET, POST, PUT, DELETE /api/subjects
    Route::apiResource('subjects', \App\Http\Controllers\Api\Academic\SubjectController::class);
    
    // POST /api/subjects/{id}/components - Add Theory/Practical components to subject
    Route::post('subjects/{subject}/components', [\App\Http\Controllers\Api\Academic\SubjectController::class, 'addComponents']);
    
    // ========================================
    // ACADEMIC SESSION MANAGEMENT
    // ========================================
    // Handles academic years like 2024-25, semesters, etc.
    // Database Table: academic_sessions
    // Creates routes: GET, POST, PUT, DELETE /api/academic-sessions
    Route::apiResource('academic-sessions', \App\Http\Controllers\Api\Academic\AcademicSessionController::class);
    
    // ========================================
    // ADMISSION MANAGEMENT (3-Step Workflow)
    // ========================================
    // Handles Apply → Verify Documents → Enroll workflow
    // Database Tables: admissions, student_documents, audit_logs
    
    // POST /api/admissions/apply - Submit admission application (public)
    Route::post('admissions/apply', [\App\Http\Controllers\Api\Academic\AdmissionController::class, 'apply']);
    
    // GET /api/admissions - List admissions with filters
    Route::get('admissions', [\App\Http\Controllers\Api\Academic\AdmissionController::class, 'index']);
    
    // GET /api/admissions/{id} - Get admission details
    Route::get('admissions/{id}', [\App\Http\Controllers\Api\Academic\AdmissionController::class, 'show']);
    
    // POST /api/admissions/{id}/documents - Upload admission documents
    Route::post('admissions/{id}/documents', [\App\Http\Controllers\Api\Academic\AdmissionController::class, 'uploadDocuments']);
    
    // POST /api/admissions/{admissionId}/documents/{documentId}/verify - Verify document
    Route::post('admissions/{admissionId}/documents/{documentId}/verify', [\App\Http\Controllers\Api\Academic\AdmissionController::class, 'verifyDocument']);
    
    // POST /api/admissions/{id}/verify - Verify admission (student_section only)
    Route::post('admissions/{id}/verify', [\App\Http\Controllers\Api\Academic\AdmissionController::class, 'verify']);
    
    // POST /api/admissions/{id}/reject - Reject admission (student_section only)
    Route::post('admissions/{id}/reject', [\App\Http\Controllers\Api\Academic\AdmissionController::class, 'reject']);

    // ========================================
    // STUDENT MANAGEMENT (Updated for Admission Workflow)
    // ========================================
    // NOW REQUIRES VERIFIED ADMISSION - POST /api/students requires admission_id
    // Database Tables: students, divisions, programs, academic_sessions
    Route::apiResource('students', \App\Http\Controllers\Api\StudentController::class);
    
    // GET /api/students/{id}/profile - Get detailed student profile with all related data
    Route::get('students/{student}/profile', [\App\Http\Controllers\Api\Academic\StudentController::class, 'profile']);
    
    // ========================================
    // GUARDIAN/PARENT MANAGEMENT
    // ========================================
    // Manages student parents/guardians information
    // Database Table: student_guardians
    // Nested resource: /api/students/{student_id}/guardians
    Route::apiResource('students.guardians', \App\Http\Controllers\Api\Academic\GuardianController::class)
        ->except(['create', 'edit']);  // Excludes form routes (API doesn't need them)
    
    // ========================================
    // DOCUMENT MANAGEMENT
    // ========================================
    // Handles student photo and signature uploads
    // Files stored in storage/app/public/students/{id}/
    Route::post('students/{student}/documents/photo', [\App\Http\Controllers\Api\Academic\DocumentController::class, 'uploadPhoto']);
    Route::post('students/{student}/documents/signature', [\App\Http\Controllers\Api\Academic\DocumentController::class, 'uploadSignature']);
    Route::get('students/{student}/documents', [\App\Http\Controllers\Api\Academic\DocumentController::class, 'getDocuments']);
    Route::delete('students/{student}/documents/photo', [\App\Http\Controllers\Api\Academic\DocumentController::class, 'deletePhoto']);
    Route::delete('students/{student}/documents/signature', [\App\Http\Controllers\Api\Academic\DocumentController::class, 'deleteSignature']);
    
    // ========================================
    // DIVISION MANAGEMENT (Class Sections)
    // ========================================
    // Manages class divisions like FY-A, SY-B, TY-C etc.
    // Database Table: divisions
    Route::apiResource('divisions', DivisionController::class);
    
    // GET /api/divisions/{id}/students - Get all students in a specific division
    Route::get('divisions/{division}/students', [DivisionController::class, 'students']);
    
    // ========================================
    // EXAMINATION MANAGEMENT
    // ========================================
    // Handles exam creation, marks entry, result processing
    // Database Tables: examinations, student_marks, subjects
    Route::apiResource('exams', \App\Http\Controllers\Api\Result\ExamController::class);
   // Route::apiResource('examinations', \App\Http\Controllers\Api\Result\ExaminationController::class);
    
    // ========================================
    // FEE MANAGEMENT (Updated for FRD Compliance)
    // ========================================
    // NEW: FRD-compliant endpoints with installment sequencing
    // Database Tables: student_fees, fee_payments, fee_structures, fee_heads
    
    // FRD-compliant endpoints
    Route::post('students/{student}/assign-fee-structure', [\App\Http\Controllers\Api\Fee\FeeController::class, 'assignFeeStructure']);
    Route::get('students/{student}/fee-ledger', [\App\Http\Controllers\Api\Fee\FeeController::class, 'getFeeLedger']);
    Route::post('fees/pay', [\App\Http\Controllers\Api\Fee\FeeController::class, 'payFee']);
    
    // Legacy endpoints (maintained for backward compatibility)
    Route::post('fees/assign', [\App\Http\Controllers\Api\Fee\FeeController::class, 'assignFees']);
    Route::get('students/{student}/fees', [FeeController::class, 'getAssignedFees']);
    Route::get('students/{student}/payments', [FeeController::class, 'getPayments']);
    Route::post('students/{student}/payment', [\App\Http\Controllers\Api\Fee\FeeController::class, 'recordPayment']);
    Route::get('students/{student}/outstanding', [\App\Http\Controllers\Api\Fee\FeeController::class, 'outstanding']);
    
    // ========================================
    // SCHOLARSHIP WORKFLOW (NEW - FRD Compliant)
    // ========================================
    // Implements Apply → Verify → Approve workflow
    // Database Tables: scholarship_applications, scholarships
    
    // POST /api/students/{id}/scholarship/apply - Apply for scholarship
    Route::post('students/{student}/scholarship/apply', [\App\Http\Controllers\Api\Fee\ScholarshipApplicationController::class, 'apply']);
    
    // POST /api/scholarship/{id}/verify - Verify scholarship (student_section only)
    Route::post('scholarship/{applicationId}/verify', [\App\Http\Controllers\Api\Fee\ScholarshipApplicationController::class, 'verify']);
    
    // GET /api/students/{id}/scholarship - Get student's scholarship applications
    Route::get('students/{student}/scholarship', [\App\Http\Controllers\Api\Fee\ScholarshipApplicationController::class, 'getStudentScholarships']);
    
    // GET /api/scholarship-applications - List all applications (admin/student_section)
    Route::get('scholarship-applications', [\App\Http\Controllers\Api\Fee\ScholarshipApplicationController::class, 'index']);
    
    // GET /api/scholarship-applications/{id} - Get application details
    Route::get('scholarship-applications/{applicationId}', [\App\Http\Controllers\Api\Fee\ScholarshipApplicationController::class, 'show']);
    
    // ========================================
    // ONLINE PAYMENTS (Razorpay Integration)
    // ========================================
    // Handles online fee payments through Razorpay gateway
    // Database Tables: fee_payments (stores payment status)
    
    // POST /api/payments/create-order - Create Razorpay payment order
    Route::post('payments/create-order', [\App\Http\Controllers\Api\Fee\PaymentController::class, 'createOrder']);
    
    // POST /api/payments/verify - Verify payment after Razorpay callback
    Route::post('payments/verify', [\App\Http\Controllers\Api\Fee\PaymentController::class, 'verifyPayment']);
    
    // ========================================
    // FEE REPORTS
    // ========================================
    // Generate various fee-related reports for management
    // Database Tables: student_fees, fee_payments (with complex queries)
    
    // GET /api/reports/outstanding - Students with pending fees
    Route::get('reports/outstanding', [\App\Http\Controllers\Api\Fee\ReportController::class, 'outstandingReport']);
    
    // GET /api/reports/collection - Daily/monthly fee collection summary
    Route::get('reports/collection', [\App\Http\Controllers\Api\Fee\ReportController::class, 'collectionReport']);
    
    // GET /api/reports/defaulters - Students who haven't paid fees on time
    Route::get('reports/defaulters', [\App\Http\Controllers\Api\Fee\ReportController::class, 'defaulterReport']);
    
    // ========================================
    // LABORATORY MANAGEMENT
    // ========================================
    // Handles lab batching for practical sessions
    // Database Tables: labs, lab_sessions, lab_batches
    
    // POST /api/labs/create-batches - Auto-create lab batches for students
    Route::post('labs/create-batches', [\App\Http\Controllers\Api\Lab\LabController::class, 'createBatches']);
    
    // GET /api/labs/sessions - Get all lab sessions
    Route::get('labs/sessions', [\App\Http\Controllers\Api\Lab\LabController::class, 'getSessions']);
    
    // POST /api/labs/sessions/{id}/attendance - Mark lab attendance
    Route::post('labs/sessions/{session}/attendance', [\App\Http\Controllers\Api\Lab\LabController::class, 'markAttendance']);
    
    // POST /api/labs/reassign-student - Move student between lab batches
    Route::post('labs/reassign-student', [\App\Http\Controllers\Api\Lab\LabController::class, 'reassignStudent']);
    
    // ========================================
    // RESULTS & EXAMINATIONS
    // ========================================
    // Handles exam marks entry, approval, and result generation
    // Database Tables: student_marks, examinations, subjects
    
    // POST /api/exams/enter-marks - Teachers enter student marks
    Route::post('exams/enter-marks', [\App\Http\Controllers\Api\Result\ExamController::class, 'enterMarks']);
    
    // POST /api/exams/approve-marks - HOD/Principal approve entered marks
    Route::post('exams/approve-marks', [\App\Http\Controllers\Api\Result\ExamController::class, 'approveMarks']);
    
    // GET /api/exams/results - Get exam results with CGPA calculations
    Route::get('exams/results', [\App\Http\Controllers\Api\Result\ExamController::class, 'getResults']);
    
    // GET /api/exams/marksheet - Generate PDF marksheet for students
    Route::get('exams/marksheet', [\App\Http\Controllers\Api\Result\ExamController::class, 'generateMarksheet']);
    
    // ========================================
    // ATTENDANCE & TIMETABLE
    // ========================================
    // Handles daily attendance and class scheduling
    // Database Tables: attendance, timetables
    



// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('attendance/mark', [AttendanceController::class, 'markAttendance']);
    Route::get('attendance/report', [AttendanceController::class, 'getAttendanceReport']);
    Route::get('attendance/defaulters', [AttendanceController::class, 'getDefaulters']);
    Route::apiResource('timetables', \App\Http\Controllers\Api\Attendance\TimetableController::class);
    Route::get('timetables/view', [\App\Http\Controllers\Api\Attendance\TimetableController::class, 'getTimetable']);
});

    
    // ========================================
    // DYNAMIC REPORTING SYSTEM
    // ========================================
    // Advanced report builder - users can create custom reports
    // Database Tables: report_templates, report_exports + all other tables for data
    Route::prefix('reports')->group(function () {
        
        // REPORT BUILDER - Drag & Drop Report Creation
        // GET /api/reports/models - Get available data models (Student, Fee, etc.)
        Route::get('models', [\App\Http\Controllers\Api\Reports\ReportBuilderController::class, 'getAvailableModels']);
        
        // GET /api/reports/columns - Get available columns for selected model
        Route::get('columns', [\App\Http\Controllers\Api\Reports\ReportBuilderController::class, 'getAvailableColumns']);
        
        // POST /api/reports/build - Build custom report with filters
        Route::post('build', [\App\Http\Controllers\Api\Reports\ReportBuilderController::class, 'buildReport']);
        
        // POST /api/reports/export - Export report to Excel/PDF
        Route::post('export', [\App\Http\Controllers\Api\Reports\ReportBuilderController::class, 'exportReport']);
        
        // GET /api/reports/exports/{id}/status - Check export progress
        Route::get('exports/{exportId}/status', [\App\Http\Controllers\Api\Reports\ReportBuilderController::class, 'getExportStatus']);
        
        // GET /api/reports/exports/{id}/download - Download completed export
        Route::get('exports/{exportId}/download', [\App\Http\Controllers\Api\Reports\ReportBuilderController::class, 'downloadExport']);
        
        // REPORT TEMPLATES - Pre-built report templates
        // Full CRUD for saving/loading report configurations
        Route::apiResource('templates', \App\Http\Controllers\Api\Reports\ReportTemplateController::class);
        
        // GET /api/reports/templates/category/{name} - Get templates by category
        Route::get('templates/category/{category}', [\App\Http\Controllers\Api\Reports\ReportTemplateController::class, 'getByCategory']);
    });
    
    // ========================================
    // LIBRARY MANAGEMENT
    // ========================================
    // Handles book lending, returns, and library operations
    // Database Tables: books, book_issues, students
    Route::prefix('library')->group(function () {
        
        // GET /api/library/books - Get all available books
        Route::get('books', [\App\Http\Controllers\Api\Library\LibraryController::class, 'getBooks']);
        
        // POST /api/library/issue - Issue book to student
        Route::post('issue', [\App\Http\Controllers\Api\Library\LibraryController::class, 'issueBook']);
        
        // POST /api/library/return - Return book and calculate fines
        Route::post('return', [\App\Http\Controllers\Api\Library\LibraryController::class, 'returnBook']);
        
        // GET /api/library/student/{id}/issues - Get books issued to specific student
        Route::get('student/{studentId}/issues', [\App\Http\Controllers\Api\Library\LibraryController::class, 'getStudentIssues']);
        
        // GET /api/library/overdue - Get overdue books with fine calculations
        Route::get('overdue', [\App\Http\Controllers\Api\Library\LibraryController::class, 'getOverdueBooks']);
    });
    
    // ========================================
    // HR & PAYROLL MANAGEMENT
    // ========================================
    // Handles staff management and salary processing
    // Database Tables: staff, salary_structures, salary_payments
    Route::prefix('hr')->group(function () {
        
        // GET /api/hr/staff - Get all staff members
        Route::get('staff', [\App\Http\Controllers\Api\HR\HRController::class, 'getStaff']);
        
        // POST /api/hr/salaries/generate - Generate monthly salaries
        Route::post('salaries/generate', [\App\Http\Controllers\Api\HR\HRController::class, 'generateSalaries']);
        
        // POST /api/hr/salaries/process-payment - Process salary payments
        Route::post('salaries/process-payment', [\App\Http\Controllers\Api\HR\HRController::class, 'processSalaryPayment']);
        
        // GET /api/hr/salaries/report - Get salary reports
        Route::get('salaries/report', [\App\Http\Controllers\Api\HR\HRController::class, 'getSalaryReport']);
        
        // GET /api/hr/salary-structures - Get salary structure templates
        Route::get('salary-structures', [\App\Http\Controllers\Api\HR\HRController::class, 'getSalaryStructures']);
    });
    
});

// ========================================
// WEBHOOK ROUTES (No Authentication Required)
// ========================================
// These routes are called by external services, not users
// They don't need login because they use other security methods

// POST /api/webhooks/razorpay - Razorpay payment gateway callback
// Called automatically when payment is completed/failed
// Uses Razorpay signature verification for security
Route::post('/webhooks/razorpay', [\App\Http\Controllers\Api\Fee\PaymentController::class, 'webhook']);

/**
 * =============================================================================
 * END OF API ROUTES
 * =============================================================================
 * 
 * SUMMARY FOR INTERNS:
 * - Total 54+ API endpoints covering complete school management
 * - All routes except login/webhooks require authentication
 * - Routes are organized by functionality (students, fees, attendance, etc.)
 * - Each route connects to specific database tables through controllers
 * - Controllers contain the actual business logic
 * - Models represent database tables
 * - Services handle complex calculations and business rules
 * 
 * NEXT STEPS:
 * 1. Check Controllers to see how each route works
 * 2. Check Models to understand database structure  
 * 3. Check Services for business logic
 * =============================================================================
 */
