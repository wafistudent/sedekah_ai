@extends('layouts.app')

@section('title', 'Request Withdrawal')

@section('content')
<div class="mx-auto max-w-2xl space-y-6">
    {{-- Page Header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Request Withdrawal</h1>
        <p class="mt-1 text-sm text-gray-600">Withdraw funds from your wallet to your DANA account</p>
    </div>

    {{-- Balance Info --}}
    <div class="rounded-lg border border-green-200 bg-green-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-green-800">
                    Available balance: <span class="font-semibold">Rp {{ number_format($balance, 0, ',', '.') }}</span>
                </p>
                <p class="mt-1 text-xs text-green-700">
                    Minimum withdrawal: Rp {{ number_format($minWithdrawal, 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>

    {{-- Withdrawal Form --}}
    <form method="POST" action="{{ route('wallet.withdrawal.store') }}" class="space-y-6 bg-white rounded-lg shadow p-6">
        @csrf

        <div>
            <label for="amount" class="block text-sm font-medium text-gray-700">Withdrawal Amount <span class="text-red-500">*</span></label>
            <div class="relative mt-1">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <span class="text-gray-500 sm:text-sm">Rp</span>
                </div>
                <input 
                    type="number" 
                    name="amount" 
                    id="amount" 
                    min="{{ $minWithdrawal }}"
                    max="{{ $balance }}"
                    required
                    class="block w-full rounded-md border-gray-300 pl-12 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    value="{{ old('amount') }}"
                    placeholder="0"
                >
            </div>
            <p class="mt-1 text-sm text-gray-500">
                Enter amount between Rp {{ number_format($minWithdrawal, 0, ',', '.') }} and Rp {{ number_format($balance, 0, ',', '.') }}
            </p>
            @error('amount')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- DANA Account Info (Read-only) --}}
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
            <p class="text-sm font-medium text-gray-700 mb-3">Withdrawal to DANA Account:</p>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Account Name:</span>
                    <span class="text-sm font-medium text-gray-900">{{ auth()->user()->dana_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Account Number:</span>
                    <span class="text-sm font-medium text-gray-900">{{ auth()->user()->dana_number }}</span>
                </div>
            </div>
        </div>

        {{-- Important Notes --}}
        <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">Important Notes:</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <ul class="list-disc list-inside space-y-1">
                            <li>Withdrawal requests will be processed within 1-3 business days</li>
                            <li>Make sure your DANA account information is correct</li>
                            <li>Minimum withdrawal amount is Rp {{ number_format($minWithdrawal, 0, ',', '.') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex justify-end space-x-3">
            <a href="{{ route('wallet.index') }}" class="rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                Cancel
            </a>
            <button 
                type="submit" 
                class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500"
            >
                Submit Withdrawal Request
            </button>
        </div>
    </form>
</div>
@endsection
