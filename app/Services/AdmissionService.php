<?php

namespace App\Services;

use App\Models\Academic\Admission;
use App\Models\Academic\StudentDocument;
use App\Models\AuditLog;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AdmissionService
{
    public function apply(array $data): Admission
    {
        return DB::transaction(function () use ($data) {
            // Generate application number
            $applicationNo = $this->generateApplicationNumber();
            
            $admission = Admission::create(array_merge($data, [
                'application_no' => $applicationNo,
                'status' => 'applied'
            ]));

            // Log the admission application
            AuditLog::logEvent(
                $admission,
                'applied',
                null,
                $admission->toArray()
            );

            return $admission;
        });
    }

    public function uploadDocument(
        Admission $admission,
        string $documentType,
        UploadedFile $file
    ): StudentDocument {
        return DB::transaction(function () use ($admission, $documentType, $file) {
            // Store file
            $path = $file->store("admissions/{$admission->id}/documents", 'public');
            
            // Create document record
            $document = StudentDocument::create([
                'admission_id' => $admission->id,
                'document_type' => $documentType,
                'file_path' => $path,
                'original_filename' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize()
            ]);

            // Log document upload
            AuditLog::logEvent(
                $document,
                'uploaded',
                null,
                $document->toArray()
            );

            return $document;
        });
    }

    public function verifyDocument(
        StudentDocument $document,
        bool $isVerified,
        string $notes = null
    ): StudentDocument {
        return DB::transaction(function () use ($document, $isVerified, $notes) {
            $oldValues = $document->toArray();
            
            $document->update([
                'is_verified' => $isVerified,
                'verified_by' => auth()->id(),
                'verified_at' => $isVerified ? now() : null,
                'verification_notes' => $notes
            ]);

            // Log document verification
            AuditLog::logEvent(
                $document,
                $isVerified ? 'verified' : 'rejected',
                $oldValues,
                $document->fresh()->toArray()
            );

            // Check if all documents are verified and update admission status
            $this->checkAndUpdateAdmissionStatus($document->admission);

            return $document;
        });
    }

    public function verifyAdmission(Admission $admission, string $notes = null): Admission
    {
        return DB::transaction(function () use ($admission, $notes) {
            if (!$admission->canBeVerified()) {
                throw new \Exception('Admission cannot be verified. Check application fee payment and document status.');
            }

            if (!$admission->hasAllDocumentsVerified()) {
                throw new \Exception('All required documents must be verified before admission verification.');
            }

            $oldValues = $admission->toArray();
            
            $admission->update([
                'status' => 'verified',
                'verified_by' => auth()->id(),
                'verified_at' => now()
            ]);

            // Log admission verification
            AuditLog::logEvent(
                $admission,
                'verified',
                $oldValues,
                $admission->fresh()->toArray()
            );

            return $admission;
        });
    }

    public function rejectAdmission(Admission $admission, string $reason): Admission
    {
        return DB::transaction(function () use ($admission, $reason) {
            $oldValues = $admission->toArray();
            
            $admission->update([
                'status' => 'rejected',
                'rejection_reason' => $reason,
                'verified_by' => auth()->id(),
                'verified_at' => now()
            ]);

            // Log admission rejection
            AuditLog::logEvent(
                $admission,
                'rejected',
                $oldValues,
                $admission->fresh()->toArray()
            );

            return $admission;
        });
    }

    private function generateApplicationNumber(): string
    {
        $year = Carbon::now()->year;
        $prefix = "APP{$year}";
        
        // Get the last application number for this year
        $lastAdmission = Admission::where('application_no', 'like', $prefix . '%')
            ->orderBy('application_no', 'desc')
            ->first();

        if ($lastAdmission) {
            $lastNumber = (int) substr($lastAdmission->application_no, strlen($prefix));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    private function checkAndUpdateAdmissionStatus(Admission $admission): void
    {
        if ($admission->isApplied() && 
            $admission->application_fee_paid && 
            $admission->hasAllDocumentsVerified()) {
            
            // Auto-verify if all conditions are met
            $this->verifyAdmission($admission, 'Auto-verified: All documents verified and fee paid');
        }
    }

    public function getAdmissionStats(array $filters = []): array
    {
        $query = Admission::query();

        if (isset($filters['academic_session_id'])) {
            $query->where('academic_session_id', $filters['academic_session_id']);
        }

        if (isset($filters['program_id'])) {
            $query->where('program_id', $filters['program_id']);
        }

        return [
            'total' => $query->count(),
            'applied' => $query->clone()->where('status', 'applied')->count(),
            'verified' => $query->clone()->where('status', 'verified')->count(),
            'rejected' => $query->clone()->where('status', 'rejected')->count(),
            'enrolled' => $query->clone()->where('status', 'enrolled')->count(),
        ];
    }
}