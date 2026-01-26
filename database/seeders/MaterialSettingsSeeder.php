<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use Illuminate\Database\Seeder;

/**
 * MaterialSettingsSeeder
 * 
 * Seed material-related application settings
 * 
 * @package Database\Seeders
 */
class MaterialSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AppSetting::updateOrCreate(
            ['key' => 'max_pdf_size'],
            [
                'value' => '50', // 50 MB default
                'type' => 'integer',
                'description' => 'Maximum PDF file size in MB for learning materials',
            ]
        );
    }
}
