@extends('layouts.app')

@section('title', 'Create Template - WhatsApp')

@section('content')
<div class="space-y-6" x-data="whatsappEditor()">
    {{-- Page Header --}}
    <div>
        <h1 class="text-xl font-bold text-gray-900 lg:text-2xl">Create WhatsApp Template</h1>
        <p class="mt-1 text-sm text-gray-600">Create a new message template for automated WhatsApp messaging</p>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('admin.whatsapp.templates.store') }}" class="space-y-6">
        @csrf

        {{-- Basic Info Card --}}
        <div class="bg-white rounded-lg shadow p-6 space-y-6">
            <h2 class="text-lg font-semibold text-gray-900">Basic Information</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Template Code --}}
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-1">
                        Template Code <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="code" 
                        id="code" 
                        required
                        value="{{ old('code') }}"
                        placeholder="WELCOME_NEW_MEMBER"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                    >
                    <p class="mt-1 text-xs text-gray-500">Unique identifier (uppercase, underscores)</p>
                    @error('code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Template Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        Template Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        required
                        value="{{ old('name') }}"
                        placeholder="Welcome New Member"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Category --}}
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-1">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="category" 
                        id="category" 
                        required
                        x-model="category"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                    >
                        <option value="member" {{ old('category', 'member') === 'member' ? 'selected' : '' }}>Member</option>
                        <option value="commission" {{ old('category') === 'commission' ? 'selected' : '' }}>Commission</option>
                        <option value="withdrawal" {{ old('category') === 'withdrawal' ? 'selected' : '' }}>Withdrawal</option>
                        <option value="general" {{ old('category') === 'general' ? 'selected' : '' }}>General</option>
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Active Status --}}
                <div class="flex items-center">
                    <input 
                        type="checkbox" 
                        name="is_active" 
                        id="is_active" 
                        value="1"
                        {{ old('is_active', true) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                    <label for="is_active" class="ml-2 text-sm font-medium text-gray-700">
                        Active (template can be used)
                    </label>
                </div>
            </div>
        </div>

        {{-- Editor & Preview Split View --}}
        <div class="grid lg:grid-cols-2 gap-6">
            {{-- Editor --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Template Editor</h2>
                @include('admin.whatsapp.components.editor', ['category' => old('category', 'member'), 'content' => old('content', '')])
            </div>

            {{-- Preview --}}
            <div class="bg-white rounded-lg shadow p-6">
                @include('admin.whatsapp.components.preview', ['content' => old('content', '')])
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row justify-end gap-3">
            <a 
                href="{{ route('admin.whatsapp.templates.index') }}" 
                class="inline-flex items-center justify-center rounded-md bg-white px-6 py-3 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
            >
                Cancel
            </a>
            <button 
                type="submit" 
                class="inline-flex items-center justify-center rounded-md bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-500"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Save Template
            </button>
        </div>
    </form>

    {{-- Test Send Modal --}}
    @include('admin.whatsapp.components.test-send-modal', ['category' => old('category', 'member')])
</div>
@endsection
