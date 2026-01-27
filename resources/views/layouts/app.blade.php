<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Sedekah MLM') }} - @yield('title', 'Dashboard')</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- Livewire Styles --}}
    @livewireStyles
</head>
<body class="bg-gray-50 font-sans antialiased" x-data="{ sidebarOpen: false }">
    <div class="min-h-screen">
        {{-- Mobile Overlay --}}
        <div 
            x-show="sidebarOpen" 
            @click="sidebarOpen = false" 
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            x-cloak
            class="fixed inset-0 z-30 bg-gray-900 bg-opacity-50 lg:hidden"
        ></div>

        {{-- Sidebar --}}
        @if (auth()->user()->hasRole('admin'))
            <x-sidebar.sidebar-admin />
        {{-- @else
            <x-sidebar.sidebar-member /> --}}
        @endif

        {{-- Header --}}
        <x-header />

        {{-- Main Content --}}
        <main class="lg:ml-64 pt-16">
            <div class="p-4 sm:p-6 lg:p-8">
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

                @if(session('warning'))
                    <div class="mb-4">
                        <x-alert type="warning" :message="session('warning')" />
                    </div>
                @endif

                @if(session('info'))
                    <div class="mb-4">
                        <x-alert type="info" :message="session('info')" />
                    </div>
                @endif

                {{-- Page Content --}}
                @yield('content')
            </div>
        </main>
    </div>

    {{-- Additional Scripts --}}
    @stack('scripts')
    
    {{-- Livewire Scripts --}}
    @livewireScripts
</body>
</html>
