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
    <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 transition-opacity">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-800">
                    PIN yang tersisa: <span class="font-semibold">{{ $currentBalance }}</span> PIN
                    @if($currentBalance < 1)
                        <span class="ml-2 text-red-600">(Saldo tidak cukup untuk registrasi reguler)</span>
                    @endif
                </p>
                <p class="text-xs text-blue-600 mt-1">
                    💡 Punya Marketing PIN? Scroll ke bawah untuk menggunakan Marketing PIN tanpa memotong saldo Anda.
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
                @endif
                @error('upline_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Marketing PIN Section --}}
        <div class="space-y-4 border-t pt-6">
            <h3 class="text-lg font-medium text-gray-900">
                Marketing PIN (Opsional)
            </h3>
            <p class="text-sm text-gray-600">
                Jika Anda memiliki kode Marketing PIN, masukkan di bawah ini. 
                <span class="font-semibold text-green-600">Registrasi tidak akan memotong PIN reguler Anda.</span>
            </p>

            <div>
                <label for="marketing_pin_code" class="block text-sm font-medium text-gray-700">
                    Kode Marketing PIN
                </label>
                <input 
                    type="text" 
                    name="marketing_pin_code" 
                    id="marketing_pin_code" 
                    maxlength="8"
                    placeholder="sedXXXXX"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base uppercase"
                    value="{{ old('marketing_pin_code') }}"
                >
                <p class="mt-1 text-xs text-gray-500">
                    Format: sedXXXXX (8 karakter). Kosongkan jika tidak memiliki Marketing PIN.
                </p>
                @error('marketing_pin_code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Visual indicator when marketing PIN is entered --}}
            <div id="marketing-pin-indicator" class="hidden rounded-md bg-green-50 border border-green-200 p-3">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-green-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <div class="ml-3">
                        <p class="text-sm text-green-700 font-medium">
                            Mode Marketing PIN Aktif
                        </p>
                        <p class="text-xs text-green-600 mt-1">
                            Registrasi akan menggunakan Marketing PIN, tidak akan memotong PIN reguler Anda.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row justify-end gap-3 border-t pt-6">
            <a href="{{ route('members.network-tree') }}" class="inline-flex items-center justify-center rounded-md bg-white px-6 py-3 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                Cancel
            </a>
            <button 
                type="submit" 
                id="submit-button"
                class="inline-flex items-center justify-center rounded-md bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                Register Member (1 PIN)
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const marketingPinInput = document.getElementById('marketing_pin_code');
    const indicator = document.getElementById('marketing-pin-indicator');
    const pinBalanceAlert = document.querySelector('.bg-blue-50'); // Existing PIN balance alert
    const submitButton = document.getElementById('submit-button');
    const currentBalance = {{ $currentBalance }};
    
    if (marketingPinInput) {
        marketingPinInput.addEventListener('input', function(e) {
            // Auto uppercase
            e.target.value = e.target.value.toUpperCase();
            
            // Show/hide indicator
            if (e.target.value.length > 0) {
                indicator.classList.remove('hidden');
                
                // Dim the PIN balance alert if marketing PIN is entered
                if (pinBalanceAlert) {
                    pinBalanceAlert.classList.add('opacity-50');
                }
                
                // Enable submit button when marketing PIN is entered
                submitButton.disabled = false;
                submitButton.textContent = 'Register Member (Marketing PIN)';
            } else {
                indicator.classList.add('hidden');
                
                // Restore PIN balance alert opacity
                if (pinBalanceAlert) {
                    pinBalanceAlert.classList.remove('opacity-50');
                }
                
                // Check regular PIN balance
                submitButton.disabled = currentBalance < 1;
                submitButton.textContent = 'Register Member (1 PIN)';
            }
        });
        
        // Set initial state based on current balance
        if (currentBalance < 1 && !marketingPinInput.value) {
            submitButton.disabled = true;
        }
    }
});
</script>
@endpush
@endsection
