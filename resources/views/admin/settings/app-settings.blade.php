@extends('layouts.app')

@section('title', 'App Settings - Admin')

@section('content')
<div class="mx-auto max-w-2xl space-y-6">
    {{-- Page Header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Application Settings</h1>
        <p class="mt-1 text-sm text-gray-600">Configure system-wide application settings</p>
    </div>

    {{-- Settings Form --}}
    <form method="POST" action="{{ route('admin.settings.app-settings.update') }}" class="space-y-6 bg-white rounded-lg shadow p-6">
        @csrf

        <div>
            <label for="registration_fee" class="block text-sm font-medium text-gray-700">
                Registration Fee <span class="text-red-500">*</span>
            </label>
            <div class="relative mt-1">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <span class="text-gray-500 sm:text-sm">Rp</span>
                </div>
                <input 
                    type="number" 
                    name="registration_fee" 
                    id="registration_fee" 
                    min="0"
                    step="1000"
                    required
                    value="{{ old('registration_fee', $registrationFee) }}"
                    class="block w-full rounded-md border-gray-300 pl-12 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
            </div>
            <p class="mt-1 text-sm text-gray-500">
                Fee charged when registering a new member
            </p>
            @error('registration_fee')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="pin_price" class="block text-sm font-medium text-gray-700">
                PIN Price <span class="text-red-500">*</span>
            </label>
            <div class="relative mt-1">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <span class="text-gray-500 sm:text-sm">Rp</span>
                </div>
                <input 
                    type="number" 
                    name="pin_price" 
                    id="pin_price" 
                    min="0"
                    step="1000"
                    required
                    value="{{ old('pin_price', $pinPrice) }}"
                    class="block w-full rounded-md border-gray-300 pl-12 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
            </div>
            <p class="mt-1 text-sm text-gray-500">
                Price per PIN point (for reference)
            </p>
            @error('pin_price')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="min_withdrawal" class="block text-sm font-medium text-gray-700">
                Minimum Withdrawal <span class="text-red-500">*</span>
            </label>
            <div class="relative mt-1">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <span class="text-gray-500 sm:text-sm">Rp</span>
                </div>
                <input 
                    type="number" 
                    name="min_withdrawal" 
                    id="min_withdrawal" 
                    min="0"
                    step="1000"
                    required
                    value="{{ old('min_withdrawal', $minWithdrawal) }}"
                    class="block w-full rounded-md border-gray-300 pl-12 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
            </div>
            <p class="mt-1 text-sm text-gray-500">
                Minimum amount members can withdraw
            </p>
            @error('min_withdrawal')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Important Note --}}
        <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">Important</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p>Changes to these settings will affect all future transactions. Existing transactions will not be affected.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex justify-end space-x-3 pt-6 border-t">
            <a href="{{ route('dashboard') }}" class="rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                Cancel
            </a>
            <button 
                type="submit" 
                class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500"
            >
                Save Settings
            </button>
        </div>
    </form>
</div>
@endsection
