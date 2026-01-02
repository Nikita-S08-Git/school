<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Fee\FeeStructure;

class FeeStructureSeeder extends Seeder
{
    public function run(): void
    {
        // BCOM Fee Structure
        FeeStructure::updateOrCreate(
            ['id' => 1],
            [
                'program_id' => 1,
                'academic_year' => '2024-25',
                'fee_head_id' => 1,
                'amount' => 5000,
                'installments' => 2,
                'is_active' => true,
            ]
        );

        // BSC Fee Structure
        FeeStructure::updateOrCreate(
            ['id' => 2],
            [
                'program_id' => 2,
                'academic_year' => '2024-25',
                'fee_head_id' => 1,
                'amount' => 5000,
                'installments' => 2,
                'is_active' => true,
            ]
        );
    }
}