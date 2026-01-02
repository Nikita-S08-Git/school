<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Fee\FeeHead;

class FeeHeadSeeder extends Seeder
{
    public function run(): void
    {
        FeeHead::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'Tuition Fee',
                'code' => 'TF',
                'description' => 'Annual tuition fee for academic year',
                'is_refundable' => false,
                'is_active' => true,
            ]
        );

        FeeHead::updateOrCreate(
            ['id' => 2],
            [
                'name' => 'Practical Fee',
                'code' => 'PF',
                'description' => 'Laboratory and practical session fee',
                'is_refundable' => false,
                'is_active' => true,
            ]
        );
    }
}