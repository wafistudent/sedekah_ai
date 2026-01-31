@extends('layouts.app')

@section('title', 'My Withdrawal Requests')

@section('content')
    <div class="space-y-6">
        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-gray-900 lg:text-2xl">My Withdrawal Requests</h1>
                <p class="mt-1 text-sm text-gray-600">Track the status of your withdrawal requests</p>
            </div>
            <a href="{{ route('wallet.withdrawal') }}"
                class="inline-flex items-center justify-center rounded-md bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                        clip-rule="evenodd" />
                </svg>
                New Withdrawal Request
            </a>
        </div>

        {{-- Withdrawals Table --}}
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Request Date</th>
                            <th scope="col"
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Amount</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DANA
                                Account</th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Processed Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($withdrawals as $withdrawal)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $withdrawal->created_at->format('d M Y, H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900">
                                    Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div>
                                        <p class="font-medium">{{ $danaAccount->dana_name }}</p>
                                        <p class="text-gray-500">{{ $danaAccount->dana_number }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span
                                        class="inline-flex rounded-full px-2 py-1 text-xs font-semibold
                                    {{ $withdrawal->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $withdrawal->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $withdrawal->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                                ">
                                        {{ ucfirst($withdrawal->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $withdrawal->processed_at ? $withdrawal->processed_at->format('d M Y, H:i') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">
                                    No withdrawal requests found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($withdrawals->hasPages())
                <div class="border-t border-gray-200 px-6 py-4">
                    {{ $withdrawals->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
