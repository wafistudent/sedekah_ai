<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use Illuminate\Database\Seeder;

/**
 * AppSettingSeeder
 * 
 * Seeds the application settings
 * 
 * @package Database\Seeders
 */
class AppSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'registration_fee',
                'value' => '20000',
                'type' => 'decimal',
                'description' => 'Fee charged when a new member registers',
            ],
            [
                'key' => 'pin_price',
                'value' => '20000',
                'type' => 'decimal',
                'description' => 'Price per PIN point',
            ],
            [
                'key' => 'min_withdrawal',
                'value' => '50000',
                'type' => 'decimal',
                'description' => 'Minimum withdrawal amount',
            ],
            [
                'key' => 'max_network_depth',
                'value' => '8',
                'type' => 'integer',
                'description' => 'Maximum network depth levels',
            ],
        ];

        foreach ($settings as $setting) {
            AppSetting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'type' => $setting['type'],
                    'description' => $setting['description'],
                ]
            );
        }
    }
}
