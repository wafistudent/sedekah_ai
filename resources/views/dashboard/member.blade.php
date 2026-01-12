@extends('layouts.app')

@section('title', 'Member Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Page Header --}}
    <div>
        <h1 class="text-xl font-bold text-gray-900 lg:text-2xl">Welcome, {{ auth()->user()->name }}!</h1>
        <p class="mt-1 text-sm text-gray-600">Your MLM dashboard and statistics</p>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 sm:gap-6">
        <x-stats-card 
            title="Wallet Balance" 
            :value="'Rp ' . number_format($walletBalance, 0, ',', '.')" 
            color="green"
            :icon="'<svg class=\'w-6 h-6\' fill=\'currentColor\' viewBox=\'0 0 20 20\'><path fill-rule=\'evenodd\' d=\'M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z\' clip-rule=\'evenodd\'/></svg>'" 
        />

        <x-stats-card 
            title="PIN Balance" 
            :value="$pinBalance" 
            color="blue"
            :icon="'<svg class=\'w-6 h-6\' fill=\'currentColor\' viewBox=\'0 0 20 20\'><path d=\'M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z\'/><path fill-rule=\'evenodd\' d=\'M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z\' clip-rule=\'evenodd\'/></svg>'" 
        />

        <x-stats-card 
            title="Total Commission" 
            :value="'Rp ' . number_format($totalCommission, 0, ',', '.')" 
            color="purple"
            :icon="'<svg class=\'w-6 h-6\' fill=\'currentColor\' viewBox=\'0 0 20 20\'><path d=\'M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z\'/><path fill-rule=\'evenodd\' d=\'M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z\' clip-rule=\'evenodd\'/></svg>'" 
        />

        <x-stats-card 
            title="Total Downlines" 
            :value="$totalDownlines" 
            color="indigo"
            :icon="'<svg class=\'w-6 h-6\' fill=\'currentColor\' viewBox=\'0 0 20 20\'><path d=\'M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z\'/></svg>'" 
        />

        <x-stats-card 
            title="Active Downlines" 
            :value="$activeDownlines" 
            color="teal"
            :icon="'<svg class=\'w-6 h-6\' fill=\'currentColor\' viewBox=\'0 0 20 20\'><path fill-rule=\'evenodd\' d=\'M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z\' clip-rule=\'evenodd\'/></svg>'" 
        />

        <x-stats-card 
            title="Pending Withdrawals" 
            :value="$pendingWithdrawals" 
            color="orange"
            :icon="'<svg class=\'w-6 h-6\' fill=\'currentColor\' viewBox=\'0 0 20 20\'><path fill-rule=\'evenodd\' d=\'M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z\' clip-rule=\'evenodd\'/></svg>'" 
        />
    </div>

    {{-- Quick Actions --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 sm:gap-6">
        <a href="{{ route('members.network-tree') }}" class="block rounded-lg border-2 border-blue-200 bg-blue-50 p-4 sm:p-6 text-center hover:bg-blue-100">
            <svg class="mx-auto h-8 w-8 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
            </svg>
            <p class="mt-2 font-medium text-blue-900">Network Tree</p>
        </a>

        <a href="{{ route('pins.reedem') }}" class="block rounded-lg border-2 border-green-200 bg-green-50 p-4 sm:p-6 text-center hover:bg-green-100">
            <svg class="mx-auto h-8 w-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/>
            </svg>
            <p class="mt-2 font-medium text-green-900">Register Member</p>
        </a>

        <a href="{{ route('wallet.withdrawal') }}" class="block rounded-lg border-2 border-purple-200 bg-purple-50 p-4 sm:p-6 text-center hover:bg-purple-100">
            <svg class="mx-auto h-8 w-8 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
            <p class="mt-2 font-medium text-purple-900">Withdraw</p>
        </a>

        <a href="{{ route('pins.transfer') }}" class="block rounded-lg border-2 border-orange-200 bg-orange-50 p-4 sm:p-6 text-center hover:bg-orange-100">
            <svg class="mx-auto h-8 w-8 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/>
                <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/>
            </svg>
            <p class="mt-2 font-medium text-orange-900">Transfer PIN</p>
        </a>
    </div>

    {{-- Recent Transactions --}}
    <div class="bg-white rounded-lg shadow">
        <div class="border-b border-gray-200 px-6 py-4">
            <h3 class="text-lg font-medium text-gray-900">Recent Transactions</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @forelse($recentTransactions as $transaction)
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $transaction->description }}</p>
                            <p class="text-xs text-gray-500"> {{ \Carbon\Carbon::parse($transaction->created_at)->format('d M Y, H:i') }}</p>
                        </div>
                        <span class="text-sm font-medium {{ $transaction->amount >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $transaction->amount >= 0 ? '+' : '' }}Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No transactions yet</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
