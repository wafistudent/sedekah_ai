@props(['content' => '', 'category' => 'member'])

<div class="space-y-4">
    {{-- Toolbar --}}
    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
        {{-- Format Buttons --}}
        <div class="flex items-center gap-2">
            <button type="button" @click="wrapText('*')" class="inline-flex items-center px-3 py-1.5 text-sm font-bold text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50" title="Bold">
                B
            </button>
            <button type="button" @click="wrapText('_')" class="inline-flex items-center px-3 py-1.5 text-sm italic text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50" title="Italic">
                I
            </button>
            <button type="button" @click="wrapText('~')" class="inline-flex items-center px-3 py-1.5 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50" title="Strikethrough">
                <span class="line-through">S</span>
            </button>
            <button type="button" @click="wrapText('```')" class="inline-flex items-center px-3 py-1.5 text-sm font-mono text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50" title="Monospace">
                M
            </button>
        </div>

        {{-- Variable Dropdown --}}
        <div class="relative" x-data="{ open: false }">
            <button type="button" @click="open = !open" class="inline-flex items-center px-4 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Insert Variable
            </button>
            <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-56 bg-white rounded-md shadow-lg border border-gray-200 z-10" x-cloak>
                <div class="py-1">
                    <button type="button" @click="insertVariable('name'); open = false" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        Name - {{name}}
                    </button>
                    <button type="button" @click="insertVariable('username'); open = false" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        Username - {{username}}
                    </button>
                    <button type="button" @click="insertVariable('email'); open = false" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        Email - {{email}}
                    </button>
                    <button type="button" @click="insertVariable('phone'); open = false" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        Phone - {{phone}}
                    </button>
                    <template x-if="category === 'commission'">
                        <div>
                            <button type="button" @click="insertVariable('amount'); open = false" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Amount - {{amount}}
                            </button>
                            <button type="button" @click="insertVariable('commission_type'); open = false" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Type - {{commission_type}}
                            </button>
                        </div>
                    </template>
                    <template x-if="category === 'withdrawal'">
                        <div>
                            <button type="button" @click="insertVariable('amount'); open = false" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Amount - {{amount}}
                            </button>
                            <button type="button" @click="insertVariable('status'); open = false" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Status - {{status}}
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Emojis --}}
    <div class="flex items-center gap-2 p-3 bg-gray-50 rounded-lg border border-gray-200">
        <span class="text-xs text-gray-600 font-medium mr-2">Quick Emoji:</span>
        <button type="button" @click="insertEmoji('👋')" class="px-2 py-1 text-lg hover:bg-white rounded">👋</button>
        <button type="button" @click="insertEmoji('✅')" class="px-2 py-1 text-lg hover:bg-white rounded">✅</button>
        <button type="button" @click="insertEmoji('🎉')" class="px-2 py-1 text-lg hover:bg-white rounded">🎉</button>
        <button type="button" @click="insertEmoji('💰')" class="px-2 py-1 text-lg hover:bg-white rounded">💰</button>
        <button type="button" @click="insertEmoji('📱')" class="px-2 py-1 text-lg hover:bg-white rounded">📱</button>
        <button type="button" @click="insertEmoji('⚠️')" class="px-2 py-1 text-lg hover:bg-white rounded">⚠️</button>
        <button type="button" @click="insertEmoji('ℹ️')" class="px-2 py-1 text-lg hover:bg-white rounded">ℹ️</button>
        <button type="button" @click="insertEmoji('🔔')" class="px-2 py-1 text-lg hover:bg-white rounded">🔔</button>
    </div>

    {{-- Textarea --}}
    <div>
        <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Message Content</label>
        <textarea 
            name="content" 
            id="content" 
            rows="12"
            x-model="content"
            @input="updatePreview()"
            placeholder="Type your message here... Use variables like {{name}} and format with *bold*, _italic_, ~strikethrough~, ```monospace```"
            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base font-sans"
        >{{ $content }}</textarea>
    </div>

    {{-- Character Counter & Validation --}}
    <div class="flex items-center justify-between text-sm">
        <div class="flex items-center gap-4">
            <span class="text-gray-600">
                <span x-text="content.length"></span> characters
            </span>
            <span :class="variablesValid ? 'text-green-600' : 'text-red-600'" class="flex items-center gap-1">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path x-show="variablesValid" fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    <path x-show="!variablesValid" fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <span x-text="variablesValid ? 'All variables valid' : 'Invalid variables detected'"></span>
            </span>
        </div>
        <div class="text-xs text-gray-500">
            Max: 4096 characters (WhatsApp limit)
        </div>
    </div>

    {{-- Available Variables --}}
    <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
        <h4 class="text-sm font-semibold text-blue-900 mb-2">Available Variables</h4>
        <div class="grid grid-cols-2 gap-2 text-xs text-blue-800">
            <div><code class="bg-white px-1 py-0.5 rounded">{{name}}</code> - Member name</div>
            <div><code class="bg-white px-1 py-0.5 rounded">{{username}}</code> - Username</div>
            <div><code class="bg-white px-1 py-0.5 rounded">{{email}}</code> - Email address</div>
            <div><code class="bg-white px-1 py-0.5 rounded">{{phone}}</code> - Phone number</div>
            @if($category === 'commission')
                <div><code class="bg-white px-1 py-0.5 rounded">{{amount}}</code> - Commission amount</div>
                <div><code class="bg-white px-1 py-0.5 rounded">{{commission_type}}</code> - Commission type</div>
            @elseif($category === 'withdrawal')
                <div><code class="bg-white px-1 py-0.5 rounded">{{amount}}</code> - Withdrawal amount</div>
                <div><code class="bg-white px-1 py-0.5 rounded">{{status}}</code> - Withdrawal status</div>
            @endif
        </div>
    </div>
</div>
