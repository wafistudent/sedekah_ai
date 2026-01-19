@extends('layouts.app')

@section('title', 'Commission Configuration - Admin')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    {{-- Page Header --}}
    <div>
        <h1 class="text-xl font-bold text-gray-900 lg:text-2xl">Commission Configuration</h1>
        <p class="mt-1 text-sm text-gray-600">Configure commission amounts for each of the 8 network levels</p>
    </div>

    {{-- Configuration Form --}}
    <form method="POST" action="{{ route('admin.settings.commission-config.update') }}" class="space-y-6">
        @csrf

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Commission Levels (1-8)</h3>
                <p class="mt-1 text-sm text-gray-500">Set the commission amount for each level</p>
            </div>

            <div class="p-6 space-y-4">
                @foreach($commissionLevels as $index => $level)
                    <div class="flex items-center space-x-4 p-4 rounded-lg border border-gray-200 bg-gray-50">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-12 w-12 rounded-full bg-purple-100">
                                <span class="text-sm font-bold text-purple-900">L{{ $level->level }}</span>
                            </div>
                        </div>

                        <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <input type="hidden" name="levels[{{ $index }}][level]" value="{{ $level->level }}">
                            
                            <div>
                                <label for="level_{{ $level->level }}_amount" class="block text-sm font-medium text-gray-700">
                                    Amount (Rp)
                                </label>
                                <input 
                                    type="number" 
                                    name="levels[{{ $index }}][amount]" 
                                    id="level_{{ $level->level }}_amount"
                                    min="0"
                                    step="1000"
                                    value="{{ old("levels.{$index}.amount", $level->amount) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base"
                                >
                            </div>

                            <div class="flex items-end">
                                <label class="flex items-center">
                                    <input 
                                        type="checkbox" 
                                        name="levels[{{ $index }}][is_active]" 
                                        value="1"
                                        {{ old("levels.{$index}.is_active", $level->is_active) ? 'checked' : '' }}
                                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    >
                                    <span class="ml-2 text-sm text-gray-700">Active</span>
                                </label>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($errors->any())
                <div class="px-6 py-4 border-t border-gray-200">
                    <div class="rounded-lg border border-red-200 bg-red-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Validation Errors</h3>
                                <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row justify-end gap-3">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-md bg-white px-6 py-3 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    Cancel
                </a>
                <button 
                    type="submit" 
                    class="inline-flex items-center justify-center rounded-md bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-500"
                >
                    Save Configuration
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
