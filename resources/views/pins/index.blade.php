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
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Points</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
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
                                    {{ $transaction->type === 'purchase' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $transaction->type === 'transfer' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $transaction->type === 'reedem' ? 'bg-purple-100 text-purple-800' : '' }}
                                ">
                                    {{ ucfirst($transaction->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $transaction->target_id ? $transaction->target_id : '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $transaction->description }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium
                                {{ $transaction->point >= 0 ? 'text-green-600' : 'text-red-600' }}
                            ">
                                {{ $transaction->point >= 0 ? '+' : '' }}{{ $transaction->point }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                                {{ $transaction->after_point }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold
                                    {{ $transaction->status === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}
                                ">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">
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
