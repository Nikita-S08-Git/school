<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class AuditLogService
{
    public static function logFeeReassignment(
        Model $student,
        array $oldFeeStructures,
        array $newFeeStructures,
        string $reason = null
    ): void {
        AuditLog::logEvent(
            $student,
            'fee_structure_reassigned',
            ['fee_structures' => $oldFeeStructures, 'reason' => $reason],
            ['fee_structures' => $newFeeStructures, 'reason' => $reason]
        );
    }

    public static function logScholarshipVerification(
        Model $scholarshipApplication,
        string $action, // 'approved' or 'rejected'
        string $notes = null
    ): void {
        AuditLog::logEvent(
            $scholarshipApplication,
            "scholarship_{$action}",
            ['status' => 'pending'],
            ['status' => $action, 'notes' => $notes]
        );
    }

    public static function logPayment(
        Model $payment,
        array $paymentData
    ): void {
        AuditLog::logEvent(
            $payment,
            'payment_recorded',
            null,
            $paymentData
        );
    }

    public static function logInstallmentViolation(
        Model $studentFee,
        int $attemptedInstallment,
        int $nextValidInstallment
    ): void {
        AuditLog::logEvent(
            $studentFee,
            'installment_sequence_violation',
            null,
            [
                'attempted_installment' => $attemptedInstallment,
                'next_valid_installment' => $nextValidInstallment,
                'violation_time' => now()
            ]
        );
    }
}