<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Sedekah MLM') }} - @yield('title', 'Authentication')</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans antialiased">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md">
            {{-- Logo --}}
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Sedekah MLM</h1>
                <p class="mt-2 text-sm text-gray-600">Multi-Level Marketing Application</p>
            </div>

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="mb-4">
                    <x-alert type="success" :message="session('success')" />
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4">
                    <x-alert type="error" :message="session('error')" />
                </div>
            @endif

            {{-- Content --}}
            <div class="bg-white rounded-lg shadow-md p-8">
                @yield('content')
            </div>

            {{-- Footer --}}
            <div class="mt-8 text-center">
                <p class="text-sm text-gray-600">
                    &copy; {{ date('Y') }} Sedekah MLM. All rights reserved.
                </p>
            </div>
        </div>
    </div>

    {{-- Additional Scripts --}}
    @stack('scripts')
</body>
</html>
