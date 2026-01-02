<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Fee\Scholarship;

class ScholarshipSeeder extends Seeder
{
    public function run(): void
    {
        Scholarship::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'Merit Scholarship',
                'code' => 'MERIT',
                'type' => 'percentage',
                'value' => 25,
                'is_active' => true,
            ]
        );

        Scholarship::updateOrCreate(
            ['id' => 2],
            [
                'name' => 'SC/ST Scholarship',
                'code' => 'SCST',
                'type' => 'percentage',
                'value' => 50,
                'is_active' => true,
            ]
        );

        Scholarship::updateOrCreate(
            ['id' => 3],
            [
                'name' => 'OBC Scholarship',
                'code' => 'OBC',
                'type' => 'percentage',
                'value' => 30,
                'is_active' => true,
            ]
        );
    }
}