<?php

namespace App\Http\Controllers;

use App\Services\WhatsappService;
use Illuminate\Http\Request;

class WhatsappController
{
     /**
     * @var WhatsappService
     */
    protected WhatsappService $whatsappService;

    public function __construct(WhatsappService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    public function index()
    {
        dd($this->whatsappService->health())->json();
    }

    public function numberCheck()
    {
        dd($this->whatsappService->checkNumber("628123456789"))->json();
    }
}
