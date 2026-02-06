<?php

namespace App\Providers;

use App\Events\CommissionReceived;
use App\Events\MemberRegistered;
use App\Events\WithdrawalApproved;
use App\Events\WithdrawalRejected;
use App\Events\WithdrawalRequested;
use App\Listeners\SendWhatsappNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * EventServiceProvider
 *
 * Registers event-listener mappings for the application
 *
 * @package App\Providers
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        MemberRegistered::class => [
            SendWhatsappNotification::class,
        ],
        CommissionReceived::class => [
            SendWhatsappNotification::class,
        ],
        WithdrawalRequested::class => [
            SendWhatsappNotification::class,
        ],
        WithdrawalApproved::class => [
            SendWhatsappNotification::class,
        ],
        WithdrawalRejected::class => [
            SendWhatsappNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
