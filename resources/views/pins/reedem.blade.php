@extends('layouts.app')

@section('title', 'Register New Member')

@section('content')
<div class="mx-auto max-w-3xl space-y-6" x-data="registerMemberForm()">
    {{-- Page Header --}}
    <div>
        <h1 class="text-xl font-bold text-gray-900 lg:text-2xl">Pendaftara Member Baru</h1>
        <p class="mt-1 text-sm text-gray-600">Redeem 1 PIN untuk mendaftarkan member baru</p>
    </div>

    {{-- Current PIN Balance Alert --}}
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
                    Pin yang tersisa: <span class="font-semibold">{{ $currentBalance }}</span> PIN
                    @if($currentBalance < 1)
                        <span class="ml-2 text-red-600">(Insufficient balance to register a member)</span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    {{-- Registration Form --}}
    <form method="POST" action="{{ route('pins.reedem.store') }}" class="space-y-6 bg-white rounded-lg shadow p-6">
        @csrf

        {{-- New Member Information --}}
        <div class="space-y-4">
            <h3 class="text-lg font-medium text-gray-900">Informasi Member Baru</h3>

            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Username <span class="text-red-500">*</span></label>
                <input 
                    type="text" 
                    name="username" 
                    id="username" 
                    x-model="form.username"
                    @blur="validateUsername"
                    required
                    minlength="3"
                    maxlength="20"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base"
                    value="{{ old('username') }}"
                >
                <p x-show="errors.username" x-text="errors.username" class="mt-1 text-sm text-red-600"></p>
                @error('username')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                <input 
                    type="text" 
                    name="name" 
                    id="name" 
                    required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base"
                    value="{{ old('name') }}"
                >
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
                <input 
                    type="email" 
                    name="email" 
                    id="email" 
                    x-model="form.email"
                    @blur="validateEmail"
                    required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base"
                    value="{{ old('email') }}"
                >
                <p x-show="errors.email" x-text="errors.email" class="mt-1 text-sm text-red-600"></p>
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">Nomer Whatsapp</label>
                <input 
                    type="number" 
                    name="phone" 
                    id="phone" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base"
                    value="{{ old('phone') }}"
                >
                @error('phone')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password <span class="text-red-500">*</span></label>
                <input 
                    type="password" 
                    name="password" 
                    id="password" 
                    x-model="form.password"
                    @blur="validatePassword"
                    required
                    minlength="8"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base"
                >
                <p x-show="errors.password" x-text="errors.password" class="mt-1 text-sm text-red-600"></p>
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password <span class="text-red-500">*</span></label>
                <input 
                    type="password" 
                    name="password_confirmation" 
                    id="password_confirmation" 
                    required
                    minlength="8"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base"
                >
                @error('password_confirmation')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- DANA Account Information --}}
        <div class="space-y-4 border-t pt-6">
            <h3 class="text-lg font-medium text-gray-900">Informasi Akun DANA</h3>

            <div>
                <label for="dana_name" class="block text-sm font-medium text-gray-700">Nama Pemilik Akun DANA <span class="text-red-500">*</span></label>
                <input 
                    type="text" 
                    name="dana_name" 
                    id="dana_name" 
                    required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base"
                    value="{{ old('dana_name') }}"
                >
                @error('dana_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="dana_number" class="block text-sm font-medium text-gray-700">Nomer Akun DANA <span class="text-red-500">*</span></label>
                <input 
                    type="text" 
                    name="dana_number" 
                    id="dana_number" 
                    required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base"
                    value="{{ old('dana_number') }}"
                >
                @error('dana_number')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Network Placement --}}
        <div class="space-y-4 border-t pt-6">
            <h3 class="text-lg font-medium text-gray-900">Informasi Upline</h3>

            <div>
                <label for="upline_id" class="block text-sm font-medium text-gray-700">Username Upline <span class="text-red-500">*</span></label>
                <select 
                    name="upline_id" 
                    id="upline_id" 
                    required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base"
                    @if($upline) disabled @endif
                >
                    <option value="">Select an upline...</option>
                    @if($upline)
                        <option value="{{ $upline->id }}" selected>{{ $upline->name }} ({{ $upline->id }})</option>
                    @endif
                </select>
                @if($upline)
                    <input type="hidden" name="upline_id" value="{{ $upline->id }}">
                    {{-- <p class="mt-1 text-sm text-gray-500">Pre-selected from network tree</p> --}}
                @endif
                @error('upline_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center">
                <input 
                    type="checkbox" 
                    name="is_marketing" 
                    id="is_marketing" 
                    value="1"
                    {{ old('is_marketing') ? 'checked' : '' }}
                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 hidden"
                >
                {{-- <label for="is_marketing" class="ml-2 block text-sm text-gray-900">
                    Marketing Member (stops upward commission distribution)
                </label> --}}
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row justify-end gap-3 border-t pt-6">
            <a href="{{ route('members.network-tree') }}" class="inline-flex items-center justify-center rounded-md bg-white px-6 py-3 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                Cancel
            </a>
            <button 
                type="submit" 
                :disabled="hasErrors() || {{ $currentBalance }} < 1"
                class="inline-flex items-center justify-center rounded-md bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                Register Member (1 PIN)
            </button>
        </div>
    </form>
</div>
@endsection
