@props(['category' => 'member'])

<div 
    x-data="testSendModal()"
    @open-test-modal.window="show = true"
    x-show="show"
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto"
>
    {{-- Overlay --}}
    <div 
        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
        @click="show = false"
    ></div>

    {{-- Modal --}}
    <div class="flex min-h-full items-center justify-center p-4">
        <div 
            class="relative bg-white rounded-lg shadow-xl max-w-lg w-full"
            @click.away="show = false"
        >
            {{-- Header --}}
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Send Test Message</h3>
                <button 
                    type="button" 
                    @click="show = false"
                    class="text-gray-400 hover:text-gray-500"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="p-6 space-y-4">
                {{-- Phone Number --}}
                <div>
                    <label for="test_phone" class="block text-sm font-medium text-gray-700 mb-1">
                        Phone Number <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="test_phone"
                        x-model="phone"
                        placeholder="+62812345678"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                    >
                    <p class="mt-1 text-xs text-gray-500">Include country code (e.g., +62 for Indonesia)</p>
                </div>

                {{-- Test Data Inputs (Dynamic by Category) --}}
                <div class="space-y-3">
                    <h4 class="text-sm font-medium text-gray-900">Test Data</h4>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Name</label>
                            <input 
                                type="text" 
                                x-model="testData.name"
                                value="John Doe"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                            >
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Username</label>
                            <input 
                                type="text" 
                                x-model="testData.username"
                                value="johndoe"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                            >
                        </div>
                    </div>

                    @if($category === 'commission')
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Amount</label>
                                <input 
                                    type="text" 
                                    x-model="testData.amount"
                                    value="Rp 100,000"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                >
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Type</label>
                                <input 
                                    type="text" 
                                    x-model="testData.commission_type"
                                    value="Direct"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                >
                            </div>
                        </div>
                    @elseif($category === 'withdrawal')
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Amount</label>
                                <input 
                                    type="text" 
                                    x-model="testData.amount"
                                    value="Rp 500,000"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                >
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                                <input 
                                    type="text" 
                                    x-model="testData.status"
                                    value="Approved"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                >
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Preview --}}
                <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <h4 class="text-xs font-semibold text-gray-700 mb-2">Preview</h4>
                    <div class="text-sm text-gray-600 whitespace-pre-wrap" x-html="parsedContent">
                        Message preview...
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 border-t border-gray-200">
                <button 
                    type="button"
                    @click="show = false"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                >
                    Batal
                </button>
                <button 
                    type="button"
                    @click="sendTest()"
                    :disabled="loading || !phone"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span x-show="!loading">Kirim Test</span>
                    <span x-show="loading" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Sending...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
