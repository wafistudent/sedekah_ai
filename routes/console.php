<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;

Schedule::command('inspire')->hourly();

// Auto retry failed WhatsApp messages every 5 minutes
Schedule::call(function () {
    $service = app(\App\Services\WhatsappLogService::class);
    $retried = $service->retryFailed();
    
    Log::info("[WhatsApp Scheduler] Auto retry executed", [
        'messages_retried' => $retried,
        'timestamp' => now()->toDateTimeString(),
    ]);
})->everyFiveMinutes();
