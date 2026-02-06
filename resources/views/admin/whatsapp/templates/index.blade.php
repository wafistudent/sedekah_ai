@extends('layouts.app')

@section('title', 'WhatsApp Templates - Admin')

@section('content')
<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900 lg:text-2xl">WhatsApp Templates</h1>
            <p class="mt-1 text-sm text-gray-600">Manage message templates for automated WhatsApp messaging</p>
        </div>
        <a href="{{ route('admin.whatsapp.templates.create') }}" class="inline-flex items-center justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Buat Template
        </a>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('admin.whatsapp.templates.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select name="category" id="category" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="">All Categories</option>
                    <option value="member" {{ request('category') === 'member' ? 'selected' : '' }}>Member</option>
                    <option value="commission" {{ request('category') === 'commission' ? 'selected' : '' }}>Commission</option>
                    <option value="withdrawal" {{ request('category') === 'withdrawal' ? 'selected' : '' }}>Withdrawal</option>
                    <option value="general" {{ request('category') === 'general' ? 'selected' : '' }}>General</option>
                </select>
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="">All Status</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <div class="flex gap-2">
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Search templates..." class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-500">
                        Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Templates Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($templates ?? [] as $template)
            <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
                <div class="p-6">
                    {{-- Header --}}
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $template->name ?? 'Template Name' }}</h3>
                            <p class="text-xs text-gray-500 mt-1">{{ $template->code ?? 'TEMPLATE_CODE' }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold
                                @if(($template->is_active ?? true)) bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800
                                @endif
                            ">
                                {{ ($template->is_active ?? true) ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>

                    {{-- Category Badge --}}
                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold
                        @if(($template->category ?? 'member') === 'member') bg-blue-100 text-blue-800
                        @elseif(($template->category ?? 'member') === 'commission') bg-purple-100 text-purple-800
                        @elseif(($template->category ?? 'member') === 'withdrawal') bg-yellow-100 text-yellow-800
                        @else bg-gray-100 text-gray-800
                        @endif
                    ">
                        {{ ucfirst($template->category ?? 'member') }}
                    </span>

                    {{-- Stats --}}
                    <div class="mt-4 grid grid-cols-2 gap-4 py-4 border-t border-gray-200">
                        <div>
                            <p class="text-xs text-gray-500">Usage</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $template->usage_count ?? 0 }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Success Rate</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $template->success_rate ?? '100' }}%</p>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="mt-4 flex items-center gap-2 pt-4 border-t border-gray-200">
                        <a href="{{ route('admin.whatsapp.templates.show', $template->id ?? 1) }}" class="flex-1 inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                            View
                        </a>
                        <a href="{{ route('admin.whatsapp.templates.edit', $template->id ?? 1) }}" class="flex-1 inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-500">
                            Edit
                        </a>
                        <form action="{{ route('admin.whatsapp.templates.update', $template->id ?? 1) }}" method="POST" class="inline-block">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="is_active" value="{{ ($template->is_active ?? true) ? 0 : 1 }}">
                            <button type="submit" class="inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50" title="{{ ($template->is_active ?? true) ? 'Deactivate' : 'Activate' }}">
                                @if(($template->is_active ?? true))
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @endif
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            {{-- Empty State --}}
            <div class="col-span-full">
                <div class="bg-white rounded-lg shadow p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No templates found</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating your first WhatsApp template.</p>
                    <div class="mt-6">
                        <a href="{{ route('admin.whatsapp.templates.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-500">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Create Template
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if(isset($templates) && method_exists($templates, 'links'))
        <div class="mt-6">
            {{ $templates->links() }}
        </div>
    @endif
</div>
@endsection
