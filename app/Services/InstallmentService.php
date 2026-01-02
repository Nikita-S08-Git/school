<?php

namespace App\Services;

use App\Models\Fee\StudentFee;
use App\Models\Fee\FeePayment;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;

class InstallmentService
{
    public function validateInstallmentSequence(StudentFee $studentFee, int $installmentNumber): bool
    {
        if ($installmentNumber <= 1) {
            return true; // First installment can always be paid
        }

        // Check if all previous installments are fully paid
        $installmentAmount = $studentFee->final_amount / $studentFee->feeStructure->installments;
        
        for ($i = 1; $i < $installmentNumber; $i++) {
            $paidAmount = FeePayment::where('student_fee_id', $studentFee->id)
                ->where('installment_number', $i)
                ->where('status', 'success')
                ->sum('amount');
                
            if ($paidAmount < $installmentAmount) {
                return false;
            }
        }
        
        return true;
    }

    public function getNextInstallmentNumber(StudentFee $studentFee): int
    {
        $totalInstallments = $studentFee->feeStructure->installments;
        $installmentAmount = $studentFee->final_amount / $totalInstallments;
        
        for ($i = 1; $i <= $totalInstallments; $i++) {
            $paidAmount = FeePayment::where('student_fee_id', $studentFee->id)
                ->where('installment_number', $i)
                ->where('status', 'success')
                ->sum('amount');
                
            if ($paidAmount < $installmentAmount) {
                return $i;
            }
        }
        
        return $totalInstallments; // All paid
    }

    public function calculateInstallmentAmount(StudentFee $studentFee, int $installmentNumber): float
    {
        return $studentFee->final_amount / $studentFee->feeStructure->installments;
    }

    public function getInstallmentStatus(StudentFee $studentFee): array
    {
        $totalInstallments = $studentFee->feeStructure->installments;
        $installmentAmount = $this->calculateInstallmentAmount($studentFee, 1);
        $status = [];
        
        for ($i = 1; $i <= $totalInstallments; $i++) {
            $paidAmount = FeePayment::where('student_fee_id', $studentFee->id)
                ->where('installment_number', $i)
                ->where('status', 'success')
                ->sum('amount');
                
            $status[] = [
                'installment_number' => $i,
                'amount' => $installmentAmount,
                'paid_amount' => $paidAmount,
                'status' => $paidAmount >= $installmentAmount ? 'paid' : 'pending',
                'can_pay' => $this->validateInstallmentSequence($studentFee, $i)
            ];
        }
        
        return $status;
    }
}