@extends('layouts.app')

@section('title', 'Transfer PIN')

@section('content')
<div class="mx-auto max-w-2xl space-y-6">
    {{-- Page Header --}}
    <div>
        <h1 class="text-xl font-bold text-gray-900 lg:text-2xl">Transfer PIN</h1>
        <p class="mt-1 text-sm text-gray-600">Transfer PIN points to another member</p>
    </div>

    {{-- Current Balance Alert --}}
    <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/>
                    <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-800">
                    Your current PIN balance: <span class="font-semibold">{{ $currentBalance }}</span> PIN
                </p>
            </div>
        </div>
    </div>

    {{-- Transfer Form --}}
    <form method="POST" action="{{ route('pins.transfer.store') }}" class="space-y-6 bg-white rounded-lg shadow p-6">
        @csrf

        <div>
            <label for="recipient_id" class="block text-sm font-medium text-gray-700">Recipient <span class="text-red-500">*</span></label>
            <select 
                name="recipient_id" 
                id="recipient_id" 
                required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base"
            >
                <option value="">Select a recipient...</option>
                @foreach($members as $member)
                    <option value="{{ $member->id }}" {{ old('recipient_id') == $member->id ? 'selected' : '' }}>
                        {{ $member->name }} ({{ $member->id }}) - {{ $member->email }}
                    </option>
                @endforeach
            </select>
            @error('recipient_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="amount" class="block text-sm font-medium text-gray-700">Amount <span class="text-red-500">*</span></label>
            <input 
                type="number" 
                name="amount" 
                id="amount" 
                min="1"
                max="{{ $currentBalance }}"
                required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base"
                value="{{ old('amount') }}"
            >
            <p class="mt-1 text-sm text-gray-500">Maximum: {{ $currentBalance }} PIN</p>
            @error('amount')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row justify-end gap-3">
            <a href="{{ route('pins.index') }}" class="inline-flex items-center justify-center rounded-md bg-white px-6 py-3 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                Cancel
            </a>
            <button 
                type="submit" 
                class="inline-flex items-center justify-center rounded-md bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-500"
            >
                Transfer PIN
            </button>
        </div>
    </form>
</div>
@endsection
