@extends('layouts.app')

@section('title', 'My Wallet')

@section('content')
<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">My Wallet</h1>
            <p class="mt-1 text-sm text-gray-600">View your wallet balance and transaction history</p>
        </div>
        <a href="{{ route('wallet.withdrawal') }}" class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
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
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Transaction History</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($transactions as $transaction)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $transaction->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold
                                    {{ $transaction->reference_type === 'commission' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $transaction->reference_type === 'withdrawal' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $transaction->reference_type === 'registration_fee' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $transaction->reference_type === 'adjustment' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                ">
                                    {{ ucfirst(str_replace('_', ' ', $transaction->reference_type)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $transaction->description }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium
                                {{ $transaction->amount >= 0 ? 'text-green-600' : 'text-red-600' }}
                            ">
                                {{ $transaction->amount >= 0 ? '+' : '' }}Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                                Rp {{ number_format($transaction->after_balance, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">
                                No transactions found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($transactions->hasPages())
            <div class="border-t border-gray-200 px-6 py-4">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
