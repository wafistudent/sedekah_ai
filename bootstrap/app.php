<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Event;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withProviders([
        \App\Providers\WhatsappServiceProvider::class,
    ])
    ->withEvents(discover: [
        __DIR__.'/../app/Listeners',
    ], then: function () {
        // Register event-listener mappings
        Event::listen(
            \App\Events\MemberRegistered::class,
            \App\Listeners\SendWhatsappNotification::class,
        );
        Event::listen(
            \App\Events\CommissionReceived::class,
            \App\Listeners\SendWhatsappNotification::class,
        );
        Event::listen(
            \App\Events\WithdrawalRequested::class,
            \App\Listeners\SendWhatsappNotification::class,
        );
        Event::listen(
            \App\Events\WithdrawalApproved::class,
            \App\Listeners\SendWhatsappNotification::class,
        );
        Event::listen(
            \App\Events\WithdrawalRejected::class,
            \App\Listeners\SendWhatsappNotification::class,
        );
    })
    ->create();
