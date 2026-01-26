@extends('layouts.app')

@section('title', 'Commission Summary')

@section('content')
<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900 lg:text-2xl">Commission Summary by Level</h1>
            <p class="mt-1 text-sm text-gray-600">Your commission breakdown across 8 network levels</p>
        </div>
        <a href="{{ route('commissions.index') }}" class="inline-flex items-center justify-center rounded-md bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
            View Full History
        </a>
    </div>

    {{-- Total Commission Card --}}
    <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg shadow-lg p-8 text-white">
        <p class="text-sm font-medium opacity-90">Total Commission Earned</p>
        <p class="mt-2 text-4xl font-bold">Rp {{ number_format($totalCommission, 0, ',', '.') }}</p>
        <p class="mt-4 text-sm opacity-75">Across all levels</p>
    </div>

    {{-- Commission by Level Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 sm:gap-6">
        @for($level = 1; $level <= 8; $level++)
            @php
                $levelData = $commissionsByLevel->firstWhere('level', $level);
                $total = $levelData->total ?? 0;
                $count = $levelData->count ?? 0;
                $colors = ['blue', 'green', 'purple', 'orange', 'red', 'indigo', 'teal', 'pink'];
                $color = $colors[$level - 1];
            @endphp
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Level {{ $level }}</p>
                        <p class="mt-2 text-2xl font-bold text-gray-900">Rp {{ number_format($total, 0, ',', '.') }}</p>
                        <p class="mt-1 text-xs text-gray-500">{{ $count }} transaction(s)</p>
                    </div>
                    <div class="rounded-full bg-{{ $color }}-100 p-3">
                        <svg class="h-6 w-6 text-{{ $color }}-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
            </div>
        @endfor
    </div>

    {{-- Level Performance Chart (Simple Table View) --}}
    <livewire:tables.⚡commission-summary-table />
</div>
@endsection
