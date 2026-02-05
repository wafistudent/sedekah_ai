<?php
namespace App\Services;

use Illuminate\Support\Env;
use Illuminate\Support\Facades\Http;

/**
 * WhatsappService
 *
 * Handles WhatsApp operations, such as health checks
 * and sending messages
 *
 * @package App\Services
 */
class WhatsappService
{
    private $whatsappUrl = 'https://api.waajo.id/go-omni-v2/public/whatsapp';
    public function health()
    {
        $header = ['apikey' => '8e1839f271a140c'];
        $health = Http::withHeaders([
          'apikey' => env('WHATSAPP_API_KEY'),
        ])->post('https://api.waajo.id/go-omni-v2/public/whatsapp/is_online', $header);
        return $health;
    }
    public function checkNumber()
    {
        $response = Http::withHeaders([
            'apikey' => env('WHATSAPP_API_KEY'),
        ])->post('https://api.waajo.id/go-omni-v2/public/whatsapp/validate_number', [
            'phone_number' => '081357153031',
        ]);

        return $response->body();
    }
}
