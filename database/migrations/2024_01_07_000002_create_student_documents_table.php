<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admission_id')->constrained('admissions');
            $table->enum('document_type', [
                'tenth_marksheet',
                'twelfth_marksheet',
                'caste_certificate',
                'income_certificate',
                'domicile_certificate',
                'migration_certificate',
                'character_certificate',
                'passport_photo',
                'signature',
                'aadhar_card',
                'other'
            ]);
            $table->string('file_path');
            $table->string('original_filename');
            $table->string('mime_type');
            $table->integer('file_size');
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->timestamp('verified_at')->nullable();
            $table->text('verification_notes')->nullable();
            $table->timestamps();
            
            $table->index(['admission_id', 'document_type']);
            $table->index(['is_verified', 'document_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_documents');
    }
};