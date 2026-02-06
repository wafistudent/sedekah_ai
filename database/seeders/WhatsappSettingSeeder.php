<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * WhatsappSettingSeeder
 * 
 * Seeds default WhatsApp system settings
 * 
 * @package Database\Seeders
 */
class WhatsappSettingSeeder extends Seeder
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
                'key' => 'api_url',
                'value' => 'https://api.waajo.id/go-omni-v2/public/whatsapp',
                'type' => 'text',
                'description' => 'Base URL untuk Waajo WhatsApp API',
            ],
            [
                'key' => 'api_key',
                'value' => '8e1839f271a140c',
                'type' => 'text',
                'description' => 'API Key untuk autentikasi Waajo',
            ],
            [
                'key' => 'is_mode_safe',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Safe mode untuk mencegah spam',
            ],
            [
                'key' => 'message_delay_seconds',
                'value' => '3',
                'type' => 'number',
                'description' => 'Jeda waktu antar pesan (detik)',
            ],
            [
                'key' => 'auto_retry_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Aktifkan auto retry untuk pesan gagal',
            ],
            [
                'key' => 'retry_delay_minutes',
                'value' => '5',
                'type' => 'number',
                'description' => 'Jeda waktu setiap retry (menit)',
            ],
            [
                'key' => 'max_retry_attempts',
                'value' => '3',
                'type' => 'number',
                'description' => 'Maksimal percobaan retry per pesan',
            ],
        ];

        DB::table('whatsapp_settings')->insert($settings);
    }
}
