@extends('layouts.app')

@section('title', 'PIN Transaction History')

@section('content')
<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900 lg:text-2xl">PIN Transaction History</h1>
            <p class="mt-1 text-sm text-gray-600">View all your PIN transactions</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('pins.transfer') }}" class="inline-flex items-center justify-center rounded-md bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                Transfer PIN
            </a>
            <a href="{{ route('pins.reedem') }}" class="inline-flex items-center justify-center rounded-md bg-green-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-green-500">
                Redeem PIN
            </a>
        </div>
    </div>

    {{-- Current Balance --}}
    <div class="bg-blue-50 rounded-lg border border-blue-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-blue-900">Current PIN Balance</p>
                <p class="mt-1 text-3xl font-bold text-blue-900">{{ auth()->user()->pin_point }}</p>
            </div>
            <div class="rounded-full bg-blue-100 p-3">
                <svg class="h-8 w-8 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/>
                    <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Transactions Table --}}
    <livewire:tables.⚡pin-transactions-table />
</div>
@endsection
