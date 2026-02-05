@extends('layouts.app')

@section('title', 'Generate Marketing PINs - Admin')

@section('content')
<div class="mx-auto max-w-2xl space-y-6">
    {{-- Page Header --}}
    <div>
        <h1 class="text-xl font-bold text-gray-900 lg:text-2xl">Generate Marketing PINs</h1>
        <p class="mt-1 text-sm text-gray-600">Create new marketing PIN codes for member registration</p>
    </div>

    {{-- Info Alert --}}
    <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">About Marketing PINs</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p>Marketing PINs are special codes used for member registration. Each PIN can only be used once.</p>
                    <ul class="mt-2 list-disc list-inside space-y-1">
                        <li>PINs are generated with format: <strong>sedXXXX</strong></li>
                        <li>Designated member is optional (for tracking purposes only)</li>
                        <li>Expiration date is optional (no expiration if not set)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Generate Form --}}
    <form method="POST" action="{{ route('admin.marketing-pins.store') }}" class="space-y-6 bg-white rounded-lg shadow p-6">
        @csrf

        {{-- Quantity --}}
        <div>
            <label for="quantity" class="block text-sm font-medium text-gray-700">
                Quantity <span class="text-red-500">*</span>
            </label>
            <input 
                type="number" 
                name="quantity" 
                id="quantity" 
                min="1"
                max="100"
                required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base"
                value="{{ old('quantity', 1) }}"
                placeholder="Enter number of PINs (1-100)"
            >
            <p class="mt-1 text-xs text-gray-500">Generate between 1 and 100 PINs at once</p>
            @error('quantity')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Designated Member --}}
        <div>
            <label for="designated_member_id" class="block text-sm font-medium text-gray-700">
                Designated Member (Optional)
            </label>
            <select 
                name="designated_member_id" 
                id="designated_member_id" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base"
            >
                <option value="">None - PINs can be used by anyone</option>
                @foreach($members as $member)
                    <option value="{{ $member->id }}" {{ old('designated_member_id') == $member->id ? 'selected' : '' }}>
                        {{ $member->name }} - {{ $member->email }}
                    </option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-gray-500">Designate these PINs to a specific member for tracking (optional)</p>
            @error('designated_member_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Expired At --}}
        <div>
            <label for="expired_at" class="block text-sm font-medium text-gray-700">
                Expiration Date (Optional)
            </label>
            <input 
                type="datetime-local" 
                name="expired_at" 
                id="expired_at" 
                min="{{ now()->addHour()->format('Y-m-d\TH:i') }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base"
                value="{{ old('expired_at') }}"
            >
            <p class="mt-1 text-xs text-gray-500">Set an expiration date for these PINs (leave empty for no expiration)</p>
            @error('expired_at')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row justify-end gap-3">
            <a href="{{ route('admin.marketing-pins.index') }}" 
                class="inline-flex items-center justify-center rounded-md bg-white px-6 py-3 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                Cancel
            </a>
            <button 
                type="submit" 
                class="inline-flex items-center justify-center rounded-md bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-500"
            >
                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Generate PINs
            </button>
        </div>
    </form>
</div>
@endsection
