@extends('layouts.app')

@section('title', 'Pending Withdrawals - Admin')

@section('content')
<div class="space-y-6">
    {{-- Page Header --}}
    <div>
        <h1 class="text-xl font-bold text-gray-900 lg:text-2xl">Pending Withdrawal Requests</h1>
        <p class="mt-1 text-sm text-gray-600">Review and process member withdrawal requests</p>
    </div>

    {{-- Withdrawals Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request Date</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DANA Account</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($withdrawals as $withdrawal)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <x-member-avatar :member="$withdrawal->user" size="sm" />
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">{{ $withdrawal->user->name }}</p>
                                        <p class="text-xs text-gray-500">@{{ $withdrawal->user->id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $withdrawal->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900">
                                Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div>
                                    <p class="font-medium">{{ $withdrawal->dana_name }}</p>
                                    <p class="text-gray-500">{{ $withdrawal->dana_number }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                <div class="flex flex-col sm:flex-row justify-center gap-2">
                                    <form method="POST" action="{{ route('admin.withdrawals.approve', $withdrawal->id) }}" class="inline">
                                        @csrf
                                        <button 
                                            type="submit"
                                            onclick="return confirm('Are you sure you want to approve this withdrawal request?')"
                                            class="w-full sm:w-auto rounded bg-green-600 px-6 py-3 text-xs font-semibold text-white shadow-sm hover:bg-green-500"
                                        >
                                            Approve
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.withdrawals.reject', $withdrawal->id) }}" class="inline">
                                        @csrf
                                        <button 
                                            type="submit"
                                            onclick="return confirm('Are you sure you want to reject this withdrawal request?')"
                                            class="w-full sm:w-auto rounded bg-red-600 px-6 py-3 text-xs font-semibold text-white shadow-sm hover:bg-red-500"
                                        >
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">
                                No pending withdrawal requests
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($withdrawals->hasPages())
            <div class="border-t border-gray-200 px-6 py-4">
                {{ $withdrawals->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
