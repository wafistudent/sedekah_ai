@extends('layouts.app')

@section('title', 'WhatsApp Logs - Admin')

@section('content')
<div class="space-y-6" x-data="whatsappLogs()">
    {{-- Page Header --}}
    <div>
        <h1 class="text-xl font-bold text-gray-900 lg:text-2xl">WhatsApp Message Logs</h1>
        <p class="mt-1 text-sm text-gray-600">View and manage WhatsApp message delivery logs</p>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600 font-medium">Total Messages</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] ?? 0 }}</p>
        </div>

        <div class="bg-green-50 rounded-lg shadow p-4">
            <p class="text-sm text-green-600 font-medium">Sent</p>
            <p class="text-2xl font-bold text-green-900 mt-1">{{ $stats['sent'] ?? 0 }}</p>
        </div>

        <div class="bg-red-50 rounded-lg shadow p-4">
            <p class="text-sm text-red-600 font-medium">Failed</p>
            <p class="text-2xl font-bold text-red-900 mt-1">{{ $stats['failed'] ?? 0 }}</p>
        </div>

        <div class="bg-yellow-50 rounded-lg shadow p-4">
            <p class="text-sm text-yellow-600 font-medium">Pending</p>
            <p class="text-2xl font-bold text-yellow-900 mt-1">{{ $stats['pending'] ?? 0 }}</p>
        </div>

        <div class="bg-blue-50 rounded-lg shadow p-4">
            <p class="text-sm text-blue-600 font-medium">Success Rate</p>
            <p class="text-2xl font-bold text-blue-900 mt-1">{{ $stats['success_rate'] ?? '100' }}%</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('admin.whatsapp.logs.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="queued" {{ request('status') === 'queued' ? 'selected' : '' }}>Queued</option>
                    <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>
            
            <div>
                <label for="template" class="block text-sm font-medium text-gray-700 mb-1">Template</label>
                <select name="template" id="template" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="">All Templates</option>
                    @foreach($templates ?? [] as $template)
                        <option value="{{ $template->id }}" {{ request('template') == $template->id ? 'selected' : '' }}>
                            {{ $template->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>

            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <div class="flex gap-2">
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Phone or recipient..." class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-500">
                        Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Bulk Actions --}}
    <div class="bg-white rounded-lg shadow p-4" x-show="selectedLogs.length > 0" x-cloak>
        <div class="flex items-center justify-between">
            <span class="text-sm text-gray-600">
                <span x-text="selectedLogs.length"></span> log(s) selected
            </span>
            <button 
                type="button"
                @click="bulkResend()"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-500"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Resend Selected
            </button>
        </div>
    </div>

    {{-- Logs Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left">
                            <input 
                                type="checkbox" 
                                @change="toggleAll()"
                                x-model="selectAll"
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Template</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recipient</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sent At</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($logs ?? [] as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <input 
                                    type="checkbox" 
                                    :value="{{ $log->id ?? 1 }}"
                                    x-model="selectedLogs"
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $log->template->name ?? 'Unknown Template' }}</p>
                                    <p class="text-xs text-gray-500">{{ $log->template->code ?? 'N/A' }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="text-sm text-gray-900">{{ $log->recipient_name ?? 'N/A' }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="text-sm text-gray-900 font-mono">{{ $log->phone ?? 'N/A' }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold
                                    @if(($log->status ?? 'pending') === 'sent') bg-green-100 text-green-800
                                    @elseif(($log->status ?? 'pending') === 'failed') bg-red-100 text-red-800
                                    @elseif(($log->status ?? 'pending') === 'queued') bg-blue-100 text-blue-800
                                    @else bg-yellow-100 text-yellow-800
                                    @endif
                                ">
                                    {{ ucfirst($log->status ?? 'pending') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ ($log->sent_at ?? $log->created_at ?? now())->format('d M Y, H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <a href="{{ route('admin.whatsapp.logs.show', $log->id ?? 1) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                    View
                                </a>
                                @if(($log->status ?? '') === 'failed')
                                    <form action="{{ route('admin.whatsapp.logs.resend', $log->id ?? 1) }}" method="POST" class="inline-block">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-900">
                                            Resend
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="mt-2">No logs found</p>
                                <p class="text-xs text-gray-400 mt-1">Message logs will appear here once templates are used</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if(isset($logs) && method_exists($logs, 'links'))
        <div class="mt-6">
            {{ $logs->links() }}
        </div>
    @endif
</div>
@endsection
