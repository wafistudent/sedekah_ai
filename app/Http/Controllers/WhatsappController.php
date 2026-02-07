<?php

namespace App\Http\Controllers;

use App\Services\WhatsappService;
use Illuminate\Http\Request;

class WhatsappController
{
    private $WhatsappService;

    public function __construct(WhatsappService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }
    public function index()
    {
        $response = $this->whatsappService->msgNewMember();
        dd($response)->json();
    }
}
