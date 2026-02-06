@props(['content' => '', 'dummyData' => []])

@php
    $defaultDummyData = [
        'name' => 'John Doe',
        'username' => 'johndoe',
        'email' => 'john@example.com',
        'phone' => '+62812345678',
        'amount' => 'Rp 100,000',
        'commission_type' => 'Direct',
        'status' => 'Approved'
    ];
    $data = array_merge($defaultDummyData, $dummyData);
@endphp

<div class="space-y-4">
    <h3 class="text-sm font-semibold text-gray-900">Live Preview</h3>
    
    {{-- WhatsApp Style Container --}}
    <div class="bg-gray-100 rounded-lg p-4 min-h-[400px]">
        <div class="flex justify-end">
            <div class="whatsapp-bubble max-w-sm">
                <div x-html="parsedContent" class="whitespace-pre-wrap break-words">
                    @if($content)
                        {!! nl2br(e($content)) !!}
                    @else
                        <span class="text-gray-400 italic">Your message preview will appear here...</span>
                    @endif
                </div>
                <div class="flex items-center justify-end gap-1 mt-2 text-xs text-gray-500">
                    <span>12:34 PM</span>
                    <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2.93 17.07A10 10 0 1117.07 2.93 10 10 0 012.93 17.07zM9 5v6h6a1 1 0 110 2H8a1 1 0 01-1-1V5a1 1 0 112 0z"/>
                    </svg>
                    <span class="text-blue-500">✓✓</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Dummy Data Used --}}
    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
        <h4 class="text-xs font-semibold text-gray-700 mb-2">Dummy Data (for preview only)</h4>
        <div class="grid grid-cols-2 gap-2 text-xs text-gray-600">
            <div class="flex justify-between">
                <span class="font-medium">Name:</span>
                <span>{{ $data['name'] }}</span>
            </div>
            <div class="flex justify-between">
                <span class="font-medium">Username:</span>
                <span>{{ $data['username'] }}</span>
            </div>
            <div class="flex justify-between">
                <span class="font-medium">Email:</span>
                <span>{{ $data['email'] }}</span>
            </div>
            <div class="flex justify-between">
                <span class="font-medium">Phone:</span>
                <span>{{ $data['phone'] }}</span>
            </div>
            @if(isset($data['amount']))
                <div class="flex justify-between">
                    <span class="font-medium">Amount:</span>
                    <span>{{ $data['amount'] }}</span>
                </div>
            @endif
            @if(isset($data['commission_type']))
                <div class="flex justify-between">
                    <span class="font-medium">Type:</span>
                    <span>{{ $data['commission_type'] }}</span>
                </div>
            @endif
            @if(isset($data['status']))
                <div class="flex justify-between">
                    <span class="font-medium">Status:</span>
                    <span>{{ $data['status'] }}</span>
                </div>
            @endif
        </div>
    </div>

    {{-- Test Send Button --}}
    <button 
        type="button" 
        @click="$dispatch('open-test-modal')"
        class="w-full inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-blue-700 bg-blue-50 border border-blue-300 rounded-md hover:bg-blue-100"
    >
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
        </svg>
        Send Test Message
    </button>
</div>
