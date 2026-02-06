@extends('layouts.app')

@section('title', 'View Template - WhatsApp')

@section('content')
<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900 lg:text-2xl">{{ $template->name ?? 'Template Details' }}</h1>
            <p class="mt-1 text-sm text-gray-600">View template details and preview</p>
        </div>
        <div class="flex items-center gap-2">
            <a 
                href="{{ route('admin.whatsapp.templates.edit', $template->id ?? 1) }}" 
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-500"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>
            <form action="{{ route('admin.whatsapp.templates.duplicate', $template->id ?? 1) }}" method="POST" class="inline-block">
                @csrf
                <button 
                    type="submit"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    Duplicate
                </button>
            </form>
            <a 
                href="{{ route('admin.whatsapp.templates.index') }}" 
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
            >
                Back
            </a>
        </div>
    </div>

    {{-- Template Details Card --}}
    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Template Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Template Code</label>
                    <p class="text-base text-gray-900 font-mono bg-gray-50 px-3 py-2 rounded">{{ $template->code ?? 'TEMPLATE_CODE' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Template Name</label>
                    <p class="text-base text-gray-900 px-3 py-2">{{ $template->name ?? 'Template Name' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Category</label>
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold
                        @if(($template->category ?? 'member') === 'member') bg-blue-100 text-blue-800
                        @elseif(($template->category ?? 'member') === 'commission') bg-purple-100 text-purple-800
                        @elseif(($template->category ?? 'member') === 'withdrawal') bg-yellow-100 text-yellow-800
                        @else bg-gray-100 text-gray-800
                        @endif
                    ">
                        {{ ucfirst($template->category ?? 'member') }}
                    </span>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold
                        @if(($template->is_active ?? true)) bg-green-100 text-green-800
                        @else bg-gray-100 text-gray-800
                        @endif
                    ">
                        {{ ($template->is_active ?? true) ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Created At</label>
                    <p class="text-base text-gray-900 px-3 py-2">{{ ($template->created_at ?? now())->format('d M Y, H:i') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Last Updated</label>
                    <p class="text-base text-gray-900 px-3 py-2">{{ ($template->updated_at ?? now())->format('d M Y, H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Card --}}
    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Usage Statistics</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-6">
                <div class="bg-blue-50 rounded-lg p-4">
                    <p class="text-sm text-blue-600 font-medium">Total Sent</p>
                    <p class="text-2xl font-bold text-blue-900 mt-1">{{ $template->usage_count ?? 0 }}</p>
                </div>

                <div class="bg-green-50 rounded-lg p-4">
                    <p class="text-sm text-green-600 font-medium">Successful</p>
                    <p class="text-2xl font-bold text-green-900 mt-1">{{ $template->success_count ?? 0 }}</p>
                </div>

                <div class="bg-red-50 rounded-lg p-4">
                    <p class="text-sm text-red-600 font-medium">Failed</p>
                    <p class="text-2xl font-bold text-red-900 mt-1">{{ $template->failed_count ?? 0 }}</p>
                </div>

                <div class="bg-purple-50 rounded-lg p-4">
                    <p class="text-sm text-purple-600 font-medium">Success Rate</p>
                    <p class="text-2xl font-bold text-purple-900 mt-1">{{ $template->success_rate ?? '100' }}%</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Content & Preview --}}
    <div class="grid lg:grid-cols-2 gap-6">
        {{-- Content --}}
        <div class="bg-white rounded-lg shadow">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Template Content</h2>
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <pre class="whitespace-pre-wrap text-sm text-gray-900 font-sans">{{ $template->content ?? 'No content available' }}</pre>
                </div>
            </div>
        </div>

        {{-- Preview --}}
        <div class="bg-white rounded-lg shadow">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Preview with Dummy Data</h2>
                <div class="bg-gray-100 rounded-lg p-4 min-h-[300px]">
                    <div class="flex justify-end">
                        <div class="whatsapp-bubble max-w-sm">
                            <div class="whitespace-pre-wrap break-words">
                                @php
                                    $content = $template->content ?? 'No content';
                                    $dummyData = [
                                        'name' => 'John Doe',
                                        'username' => 'johndoe',
                                        'email' => 'john@example.com',
                                        'phone' => '+62812345678',
                                        'amount' => 'Rp 100,000',
                                        'commission_type' => 'Direct',
                                        'status' => 'Approved'
                                    ];
                                    
                                    // Replace variables
                                    foreach ($dummyData as $key => $value) {
                                        $content = str_replace("{{{$key}}}", $value, $content);
                                    }
                                    
                                    echo nl2br(e($content));
                                @endphp
                            </div>
                            <div class="flex items-center justify-end gap-1 mt-2 text-xs text-gray-500">
                                <span>12:34 PM</span>
                                <span class="text-blue-500">✓✓</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
