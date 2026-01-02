<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admissions', function (Blueprint $table) {
            $table->id();
            $table->string('application_no')->unique(); // APP2025001
            $table->enum('status', ['applied', 'verified', 'rejected', 'enrolled'])->default('applied');
            
            // Personal Information
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('blood_group')->nullable();
            $table->string('religion')->nullable();
            $table->string('caste')->nullable();
            $table->enum('category', ['general', 'sc', 'st', 'obc', 'ews'])->default('general');
            $table->string('aadhar_number', 12)->nullable();
            
            // Contact Information
            $table->string('mobile_number', 15);
            $table->string('email')->unique();
            $table->text('current_address');
            $table->text('permanent_address');
            
            // Academic Information
            $table->foreignId('program_id')->constrained('programs');
            $table->string('academic_year', 10); // FY, SY, TY
            $table->foreignId('division_id')->nullable()->constrained('divisions');
            $table->foreignId('academic_session_id')->constrained('academic_sessions');
            
            // Previous Education
            $table->string('tenth_board')->nullable();
            $table->decimal('tenth_percentage', 5, 2)->nullable();
            $table->string('twelfth_board')->nullable();
            $table->decimal('twelfth_percentage', 5, 2)->nullable();
            
            // Application Processing
            $table->foreignId('student_id')->nullable()->constrained('students');
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->timestamp('verified_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->decimal('application_fee', 8, 2)->default(0);
            $table->boolean('application_fee_paid')->default(false);
            
            $table->timestamps();
            
            $table->index(['status', 'program_id']);
            $table->index(['academic_session_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admissions');
    }
};