<?php

namespace Database\Seeders;

use App\Models\CommissionConfig;
use Illuminate\Database\Seeder;

/**
 * CommissionConfigSeeder
 * 
 * Seeds the commission configuration for 8 levels
 * 
 * @package Database\Seeders
 */
class CommissionConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $commissions = [
            1 => 40000,
            2 => 10000,
            3 => 2000,
            4 => 0,
            5 => 0,
            6 => 0,
            7 => 0,
            8 => 0,
        ];

        foreach ($commissions as $level => $amount) {
            CommissionConfig::updateOrCreate(
                ['level' => $level],
                [
                    'amount' => $amount,
                    'is_active' => true,
                ]
            );
        }
    }
}
