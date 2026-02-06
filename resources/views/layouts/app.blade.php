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
    @vite(['resources/css/app.css', 'resources/css/whatsapp.css', 'resources/js/app.js'])
    
    {{-- Livewire Styles --}}
    @livewireStyles
</head>
<body class="bg-gray-50 antialiased" x-data="{ sidebarOpen: false }">
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
        @else
            <x-sidebar.sidebar-member />
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

    {{-- Toast Notification --}}
    <div 
        x-data="toastNotification()" 
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-out duration-300"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        x-cloak
        class="fixed bottom-4 right-4 z-50 max-w-sm"
    >
        <div :class="bgClass" class="rounded-lg border p-4 shadow-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg x-show="type === 'success'" class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <svg x-show="type === 'error'" class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20" x-cloak>
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <svg x-show="type === 'warning'" class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" x-cloak>
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <svg x-show="type === 'info'" class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20" x-cloak>
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium" :class="{
                        'text-green-800': type === 'success',
                        'text-red-800': type === 'error',
                        'text-yellow-800': type === 'warning',
                        'text-blue-800': type === 'info'
                    }" x-text="message"></p>
                </div>
                <button 
                    @click="show = false" 
                    class="ml-4 inline-flex flex-shrink-0 text-gray-400 hover:text-gray-500"
                >
                    <span class="text-xl">&times;</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Additional Scripts --}}
    @stack('scripts')
    
    {{-- Livewire Scripts --}}
    @livewireScripts
</body>
</html>
