<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\WhatsappService;
use App\Services\WhatsappTemplateService;
use App\Services\WhatsappMessageService;
use App\Services\WhatsappLogService;

/**
 * WhatsappServiceProvider
 *
 * Registers WhatsApp services as singletons
 *
 * @package App\Providers
 */
class WhatsappServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register as singletons so same instance is reused
        $this->app->singleton(WhatsappService::class);
        $this->app->singleton(WhatsappTemplateService::class);
        $this->app->singleton(WhatsappMessageService::class);
        $this->app->singleton(WhatsappLogService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
