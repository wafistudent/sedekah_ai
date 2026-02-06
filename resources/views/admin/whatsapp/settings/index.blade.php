@extends('layouts.app')

@section('title', 'WhatsApp Settings - Admin')

@section('content')
<div class="space-y-6">
    {{-- Page Header --}}
    <div>
        <h1 class="text-xl font-bold text-gray-900 lg:text-2xl">WhatsApp Settings</h1>
        <p class="mt-1 text-sm text-gray-600">Configure WhatsApp API and messaging settings</p>
    </div>

    {{-- Settings Form --}}
    <form method="POST" action="{{ route('admin.whatsapp.settings.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- API Configuration --}}
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 space-y-6">
                <div class="flex items-center justify-between border-b border-gray-200 pb-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">API Configuration</h2>
                        <p class="mt-1 text-sm text-gray-600">Configure WhatsApp API connection settings</p>
                    </div>
                </div>

                <div class="space-y-4">
                    {{-- API URL --}}
                    <div>
                        <label for="api_url" class="block text-sm font-medium text-gray-700 mb-1">
                            API URL <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="url" 
                            name="api_url" 
                            id="api_url" 
                            required
                            value="{{ old('api_url', $settings['api_url'] ?? 'https://api.whatsapp.com') }}"
                            placeholder="https://api.whatsapp.com"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                        >
                        <p class="mt-1 text-xs text-gray-500">Enter your WhatsApp API endpoint URL</p>
                        @error('api_url')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- API Key --}}
                    <div>
                        <label for="api_key" class="block text-sm font-medium text-gray-700 mb-1">
                            API Key <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="password" 
                            name="api_key" 
                            id="api_key" 
                            required
                            value="{{ old('api_key', $settings['api_key'] ?? '') }}"
                            placeholder="Enter your API key"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm font-mono"
                        >
                        <p class="mt-1 text-xs text-gray-500">Your WhatsApp API authentication key</p>
                        @error('api_key')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Safe Mode --}}
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex-1">
                            <label for="is_mode_safe" class="block text-sm font-medium text-gray-900">
                                Safe Mode (Test Mode)
                            </label>
                            <p class="text-xs text-gray-500 mt-1">When enabled, messages are logged but not actually sent</p>
                        </div>
                        <div class="ml-4">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input 
                                    type="checkbox" 
                                    name="is_mode_safe" 
                                    id="is_mode_safe" 
                                    value="1"
                                    {{ old('is_mode_safe', $settings['is_mode_safe'] ?? false) ? 'checked' : '' }}
                                    class="sr-only peer"
                                >
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Message Delays --}}
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 space-y-6">
                <div class="flex items-center justify-between border-b border-gray-200 pb-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Message Delays</h2>
                        <p class="mt-1 text-sm text-gray-600">Configure delays between messages to avoid rate limiting</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Min Delay --}}
                    <div>
                        <label for="min_delay" class="block text-sm font-medium text-gray-700 mb-1">
                            Minimum Delay (seconds)
                        </label>
                        <input 
                            type="number" 
                            name="min_delay" 
                            id="min_delay" 
                            min="0"
                            step="1"
                            value="{{ old('min_delay', $settings['min_delay'] ?? 1) }}"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                        >
                        <p class="mt-1 text-xs text-gray-500">Minimum wait time between messages</p>
                        @error('min_delay')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Max Delay --}}
                    <div>
                        <label for="max_delay" class="block text-sm font-medium text-gray-700 mb-1">
                            Maximum Delay (seconds)
                        </label>
                        <input 
                            type="number" 
                            name="max_delay" 
                            id="max_delay" 
                            min="0"
                            step="1"
                            value="{{ old('max_delay', $settings['max_delay'] ?? 5) }}"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                        >
                        <p class="mt-1 text-xs text-gray-500">Maximum wait time between messages</p>
                        @error('max_delay')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Batch Size --}}
                    <div>
                        <label for="batch_size" class="block text-sm font-medium text-gray-700 mb-1">
                            Batch Size
                        </label>
                        <input 
                            type="number" 
                            name="batch_size" 
                            id="batch_size" 
                            min="1"
                            max="100"
                            value="{{ old('batch_size', $settings['batch_size'] ?? 10) }}"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                        >
                        <p class="mt-1 text-xs text-gray-500">Number of messages per batch</p>
                        @error('batch_size')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Auto Retry Configuration --}}
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 space-y-6">
                <div class="flex items-center justify-between border-b border-gray-200 pb-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Auto Retry Configuration</h2>
                        <p class="mt-1 text-sm text-gray-600">Configure automatic retry for failed messages</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Enable Auto Retry --}}
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg col-span-full">
                        <div class="flex-1">
                            <label for="enable_auto_retry" class="block text-sm font-medium text-gray-900">
                                Enable Auto Retry
                            </label>
                            <p class="text-xs text-gray-500 mt-1">Automatically retry failed messages</p>
                        </div>
                        <div class="ml-4">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input 
                                    type="checkbox" 
                                    name="enable_auto_retry" 
                                    id="enable_auto_retry" 
                                    value="1"
                                    {{ old('enable_auto_retry', $settings['enable_auto_retry'] ?? true) ? 'checked' : '' }}
                                    class="sr-only peer"
                                >
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>

                    {{-- Max Retry Attempts --}}
                    <div>
                        <label for="max_retry_attempts" class="block text-sm font-medium text-gray-700 mb-1">
                            Max Retry Attempts
                        </label>
                        <input 
                            type="number" 
                            name="max_retry_attempts" 
                            id="max_retry_attempts" 
                            min="1"
                            max="10"
                            value="{{ old('max_retry_attempts', $settings['max_retry_attempts'] ?? 3) }}"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                        >
                        <p class="mt-1 text-xs text-gray-500">Maximum number of retry attempts</p>
                        @error('max_retry_attempts')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Retry Delay --}}
                    <div>
                        <label for="retry_delay" class="block text-sm font-medium text-gray-700 mb-1">
                            Retry Delay (minutes)
                        </label>
                        <input 
                            type="number" 
                            name="retry_delay" 
                            id="retry_delay" 
                            min="1"
                            value="{{ old('retry_delay', $settings['retry_delay'] ?? 5) }}"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                        >
                        <p class="mt-1 text-xs text-gray-500">Wait time before retry attempt</p>
                        @error('retry_delay')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row justify-end gap-3">
            <button 
                type="submit" 
                class="inline-flex items-center justify-center rounded-md bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-500"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Save Settings
            </button>
        </div>
    </form>
</div>
@endsection
