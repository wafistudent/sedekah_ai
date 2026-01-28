@extends('layouts.app')

@section('title', 'Purchase PIN - Admin')

@section('content')
<div class="mx-auto max-w-2xl space-y-6">
    {{-- Page Header --}}
    <div>
        <h1 class="text-xl font-bold text-gray-900 lg:text-2xl">Purchase PIN for Member</h1>
        <p class="mt-1 text-sm text-gray-600">Add PIN points to a member's account</p>
    </div>

    {{-- Purchase Form --}}
    <form method="POST" action="{{ route('admin.pins.purchase.store') }}" class="space-y-6 bg-white rounded-lg shadow p-6">
        @csrf

        <div>
            <label for="member_id" class="block text-sm font-medium text-gray-700">Select Member <span class="text-red-500">*</span></label>
            <select 
                name="member_id" 
                id="member_id" 
                required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base"
            >
                <option value="">Select a member...</option>
                @foreach($members as $member)
                    <option value="{{ $member->id }}" {{ old('member_id') == $member->id ? 'selected' : '' }}>
                        {{ $member->name }} ({{ $member->id }}) - {{ $member->email }}
                    </option>
                @endforeach
            </select>
            @error('member_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="points" class="block text-sm font-medium text-gray-700">PIN Points <span class="text-red-500">*</span></label>
            <input 
                type="number" 
                name="points" 
                id="points" 
                min="1"
                required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base"
                value="{{ old('points') }}"
                placeholder="Enter number of PIN points"
            >
            @error('points')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
            <textarea 
                name="description" 
                id="description" 
                rows="3"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base"
                placeholder="Optional description for this PIN purchase"
            >{{ old('description') }}</textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row justify-end gap-3">
            <a href="{{ route('admin.members') }}" class="inline-flex items-center justify-center rounded-md bg-white px-6 py-3 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                Cancel
            </a>
            <button 
                type="submit" 
                class="inline-flex items-center justify-center rounded-md bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-500"
            >
                Purchase PIN
            </button>
        </div>
    </form>
</div>
@endsection
