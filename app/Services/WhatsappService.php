<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsappService
{
  private $baseUrl = "https://api.waajo.id/go-omni-v2/public/whatsapp";
  public function msgNewMember($number)
  {
    $request = Http::withHeaders([
      'apikey' => ''
    ])->post($baseUrl . '/msg-new-member', [
      
    ]);
  }
}