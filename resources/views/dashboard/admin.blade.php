@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Page Header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Admin Dashboard</h1>
        <p class="mt-1 text-sm text-gray-600">System overview and recent activities</p>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <x-stats-card 
            title="Total Members" 
            :value="$totalMembers" 
            color="blue"
            :icon="'<svg class=\'w-6 h-6\' fill=\'currentColor\' viewBox=\'0 0 20 20\'><path d=\'M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z\'/></svg>'" 
        />

        <x-stats-card 
            title="Total Wallet Balance" 
            :value="'Rp ' . number_format($totalWalletBalance, 0, ',', '.')" 
            color="green"
            :icon="'<svg class=\'w-6 h-6\' fill=\'currentColor\' viewBox=\'0 0 20 20\'><path fill-rule=\'evenodd\' d=\'M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z\' clip-rule=\'evenodd\'/></svg>'" 
        />

        <x-stats-card 
            title="Pending Withdrawals" 
            :value="$pendingWithdrawals" 
            color="orange"
            :icon="'<svg class=\'w-6 h-6\' fill=\'currentColor\' viewBox=\'0 0 20 20\'><path fill-rule=\'evenodd\' d=\'M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z\' clip-rule=\'evenodd\'/></svg>'" 
        />

        <x-stats-card 
            title="Total PIN Points" 
            :value="$totalPinPoints" 
            color="purple"
            :icon="'<svg class=\'w-6 h-6\' fill=\'currentColor\' viewBox=\'0 0 20 20\'><path d=\'M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z\'/><path fill-rule=\'evenodd\' d=\'M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z\' clip-rule=\'evenodd\'/></svg>'" 
        />
    </div>

    {{-- Recent Members and Transactions --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- Recent Members --}}
        <div class="bg-white rounded-lg shadow">
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-medium text-gray-900">Recent Members</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @forelse($recentMembers as $member)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <x-member-avatar :member="$member" size="sm" />
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $member->name }}</p>
                                    <p class="text-xs text-gray-500">@{{ $member->id }}</p>
                                </div>
                            </div>
                            <span class="text-xs text-gray-500">{{ $member->created_at->diffForHumans() }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No members yet</p>
                    @endforelse
                </div>
            </div>
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
                            <div class="flex items-center space-x-3">
                                <x-member-avatar :member="$transaction->wallet->user" size="sm" />
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $transaction->wallet->user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $transaction->description }}</p>
                                </div>
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
</div>
@endsection
