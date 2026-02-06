@extends('layouts.app')

@section('title', 'Log Details - WhatsApp')

@section('content')
<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900 lg:text-2xl">Message Log Details</h1>
            <p class="mt-1 text-sm text-gray-600">Log ID: #{{ $log->id ?? '1' }}</p>
        </div>
        <div class="flex items-center gap-2">
            @if(($log->status ?? '') === 'failed')
                <form action="{{ route('admin.whatsapp.logs.resend', $log->id ?? 1) }}" method="POST" class="inline-block">
                    @csrf
                    <button 
                        type="submit"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-500"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Resend
                    </button>
                </form>
            @endif
            <a 
                href="{{ route('admin.whatsapp.logs.index') }}" 
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
            >
                Back to Logs
            </a>
        </div>
    </div>

    {{-- Log Header --}}
    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Template</label>
                    <p class="text-base font-semibold text-gray-900">{{ $log->template->name ?? 'Unknown Template' }}</p>
                    <p class="text-xs text-gray-500 font-mono">{{ $log->template->code ?? 'N/A' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Recipient</label>
                    <p class="text-base text-gray-900">{{ $log->recipient_name ?? 'N/A' }}</p>
                    <p class="text-sm text-gray-600 font-mono">{{ $log->phone ?? 'N/A' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold
                        @if(($log->status ?? 'pending') === 'sent') bg-green-100 text-green-800
                        @elseif(($log->status ?? 'pending') === 'failed') bg-red-100 text-red-800
                        @elseif(($log->status ?? 'pending') === 'queued') bg-blue-100 text-blue-800
                        @else bg-yellow-100 text-yellow-800
                        @endif
                    ">
                        {{ ucfirst($log->status ?? 'pending') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Timeline --}}
    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Timeline</h2>
            
            <div class="flow-root">
                <ul class="-mb-8">
                    {{-- Created --}}
                    <li>
                        <div class="relative pb-8">
                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"></span>
                            <div class="relative flex space-x-3">
                                <div>
                                    <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                        <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                    </span>
                                </div>
                                <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                    <div>
                                        <p class="text-sm text-gray-900">Log Created</p>
                                        <p class="text-xs text-gray-500">Message queued for sending</p>
                                    </div>
                                    <div class="whitespace-nowrap text-right text-sm text-gray-500">
                                        {{ ($log->created_at ?? now())->format('d M Y, H:i') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>

                    {{-- Queued --}}
                    @if(($log->queued_at ?? null))
                    <li>
                        <div class="relative pb-8">
                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"></span>
                            <div class="relative flex space-x-3">
                                <div>
                                    <span class="h-8 w-8 rounded-full bg-yellow-500 flex items-center justify-center ring-8 ring-white">
                                        <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                                        </svg>
                                    </span>
                                </div>
                                <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                    <div>
                                        <p class="text-sm text-gray-900">Queued</p>
                                        <p class="text-xs text-gray-500">Message added to queue</p>
                                    </div>
                                    <div class="whitespace-nowrap text-right text-sm text-gray-500">
                                        {{ $log->queued_at->format('d M Y, H:i') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    @endif

                    {{-- Sent/Failed --}}
                    <li>
                        <div class="relative pb-8">
                            <div class="relative flex space-x-3">
                                <div>
                                    <span class="h-8 w-8 rounded-full {{ ($log->status ?? 'pending') === 'sent' ? 'bg-green-500' : 'bg-red-500' }} flex items-center justify-center ring-8 ring-white">
                                        @if(($log->status ?? 'pending') === 'sent')
                                            <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        @else
                                            <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                    </span>
                                </div>
                                <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                    <div>
                                        <p class="text-sm text-gray-900">{{ ($log->status ?? 'pending') === 'sent' ? 'Sent Successfully' : 'Failed to Send' }}</p>
                                        <p class="text-xs text-gray-500">{{ ($log->status ?? 'pending') === 'sent' ? 'Message delivered' : 'Message delivery failed' }}</p>
                                    </div>
                                    <div class="whitespace-nowrap text-right text-sm text-gray-500">
                                        {{ ($log->sent_at ?? $log->updated_at ?? now())->format('d M Y, H:i') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Message Content & Metadata --}}
    <div class="grid lg:grid-cols-2 gap-6">
        {{-- Message Content --}}
        <div class="bg-white rounded-lg shadow">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Message Content</h2>
                <div class="bg-gray-100 rounded-lg p-4">
                    <div class="flex justify-end">
                        <div class="whatsapp-bubble max-w-sm">
                            <div class="whitespace-pre-wrap break-words">
                                {{ $log->message_content ?? 'No content available' }}
                            </div>
                            <div class="flex items-center justify-end gap-1 mt-2 text-xs text-gray-500">
                                <span>{{ ($log->sent_at ?? now())->format('H:i') }}</span>
                                @if(($log->status ?? '') === 'sent')
                                    <span class="text-blue-500">✓✓</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Metadata --}}
        <div class="bg-white rounded-lg shadow">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Metadata</h2>
                <div class="space-y-3">
                    <div class="flex justify-between py-2 border-b border-gray-200">
                        <span class="text-sm font-medium text-gray-500">Message ID</span>
                        <span class="text-sm text-gray-900 font-mono">{{ $log->message_id ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-200">
                        <span class="text-sm font-medium text-gray-500">Category</span>
                        <span class="text-sm text-gray-900">{{ ucfirst($log->template->category ?? 'N/A') }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-200">
                        <span class="text-sm font-medium text-gray-500">Retry Count</span>
                        <span class="text-sm text-gray-900">{{ $log->retry_count ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-200">
                        <span class="text-sm font-medium text-gray-500">Created At</span>
                        <span class="text-sm text-gray-900">{{ ($log->created_at ?? now())->format('d M Y, H:i:s') }}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-sm font-medium text-gray-500">Updated At</span>
                        <span class="text-sm text-gray-900">{{ ($log->updated_at ?? now())->format('d M Y, H:i:s') }}</span>
                    </div>
                </div>

                {{-- Error Message (if failed) --}}
                @if(($log->status ?? '') === 'failed' && ($log->error_message ?? null))
                    <div class="mt-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <h3 class="text-sm font-semibold text-red-900 mb-2">Error Message</h3>
                        <p class="text-sm text-red-800">{{ $log->error_message }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
