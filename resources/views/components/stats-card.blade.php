@props(['title', 'value', 'icon' => null, 'color' => 'blue'])

@php
    $colorClasses = match($color) {
        'blue' => 'bg-blue-500',
        'green' => 'bg-green-500',
        'purple' => 'bg-purple-500',
        'orange' => 'bg-orange-500',
        'red' => 'bg-red-500',
        'indigo' => 'bg-indigo-500',
        'teal' => 'bg-teal-500',
        'pink' => 'bg-pink-500',
        default => 'bg-blue-500',
    };
@endphp

<div class="bg-white rounded-lg shadow p-6">
    <div class="flex items-center">
        @if($icon)
        <div class="flex-shrink-0">
            <div class="inline-flex items-center justify-center w-12 h-12 {{ $colorClasses }} rounded-lg text-white">
                {!! $icon !!}
            </div>
        </div>
        @endif
        <div class="@if($icon) ml-4 @endif flex-1">
            <p class="text-sm font-medium text-gray-500">{{ $title }}</p>
            <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $value }}</p>
        </div>
    </div>
</div>
