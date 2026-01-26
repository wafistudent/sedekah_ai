@extends('layouts.app')

@section('title', 'My Wallet')

@section('content')
<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900 lg:text-2xl">My Wallet</h1>
            <p class="mt-1 text-sm text-gray-600">View your wallet balance and transaction history</p>
        </div>
        <a href="{{ route('wallet.withdrawal') }}" class="inline-flex items-center justify-center rounded-md bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
            Request Withdrawal
        </a>
    </div>

    {{-- Balance Card --}}
    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow-lg p-8 text-white">
        <p class="text-sm font-medium opacity-90">Current Balance</p>
        <p class="mt-2 text-4xl font-bold">Rp {{ number_format($balance, 0, ',', '.') }}</p>
        <p class="mt-4 text-sm opacity-75">Available for withdrawal</p>
    </div>

    {{-- Transaction History --}}
    <div>
        <div class="mb-4">
            <h3 class="text-lg font-medium text-gray-900">Transaction History</h3>
        </div>
        <livewire:tables.⚡wallet-transactions-table />
    </div>
</div>
@endsection
