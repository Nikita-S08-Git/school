    // ========================================
    // ADMISSION MANAGEMENT (3-Step Workflow)
    // ========================================
    // Handles Apply → Verify Documents → Enroll workflow
    // Database Tables: admissions, student_documents, audit_logs
    
    // POST /api/admissions/apply - Submit admission application
    Route::post('admissions/apply', [\\App\\Http\\Controllers\\Api\\Academic\\AdmissionController::class, 'apply']);
    
    // GET /api/admissions - List admissions with filters
    Route::get('admissions', [\\App\\Http\\Controllers\\Api\\Academic\\AdmissionController::class, 'index']);
    
    // GET /api/admissions/{id} - Get admission details
    Route::get('admissions/{id}', [\\App\\Http\\Controllers\\Api\\Academic\\AdmissionController::class, 'show']);
    
    // POST /api/admissions/{id}/documents - Upload admission documents
    Route::post('admissions/{id}/documents', [\\App\\Http\\Controllers\\Api\\Academic\\AdmissionController::class, 'uploadDocuments']);
    
    // POST /api/admissions/{admissionId}/documents/{documentId}/verify - Verify document
    Route::post('admissions/{admissionId}/documents/{documentId}/verify', [\\App\\Http\\Controllers\\Api\\Academic\\AdmissionController::class, 'verifyDocument']);
    
    // POST /api/admissions/{id}/verify - Verify admission (student_section only)
    Route::post('admissions/{id}/verify', [\\App\\Http\\Controllers\\Api\\Academic\\AdmissionController::class, 'verify']);
    
    // POST /api/admissions/{id}/reject - Reject admission (student_section only)
    Route::post('admissions/{id}/reject', [\\App\\Http\\Controllers\\Api\\Academic\\AdmissionController::class, 'reject']);
