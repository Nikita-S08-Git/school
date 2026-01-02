<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fee_payments', function (Blueprint $table) {
            $table->integer('installment_number')->after('student_fee_id');
            $table->date('due_date')->after('payment_date');
            
            $table->index(['student_fee_id', 'installment_number']);
        });
    }

    public function down(): void
    {
        Schema::table('fee_payments', function (Blueprint $table) {
            $table->dropIndex(['student_fee_id', 'installment_number']);
            $table->dropColumn(['installment_number', 'due_date']);
        });
    }
};