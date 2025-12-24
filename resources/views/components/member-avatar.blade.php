@props(['member', 'size' => 'md', 'color' => null])

@php
    // Get initials from member name
    $name = $member->name ?? 'User';
    $words = explode(' ', trim($name));
    $initials = '';
    
    if (count($words) >= 2) {
        $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
    } else {
        $initials = strtoupper(substr($name, 0, 2));
    }
    
    // Size classes
    $sizeClasses = match($size) {
        'sm' => 'w-8 h-8 text-xs',
        'md' => 'w-10 h-10 text-sm',
        'lg' => 'w-12 h-12 text-base',
        'xl' => 'w-16 h-16 text-lg',
        default => 'w-10 h-10 text-sm',
    };
    
    // Color based on member ID if not provided
    if (!$color) {
        $colors = ['blue', 'green', 'purple', 'orange', 'red', 'indigo', 'teal', 'pink'];
        $memberId = $member->id ?? 'default';
        $colorIndex = abs(crc32($memberId)) % count($colors);
        $color = $colors[$colorIndex];
    }
    
    $colorClass = "avatar-{$color}";
@endphp

<div class="inline-flex items-center justify-center {{ $sizeClasses }} {{ $colorClass }} rounded-full text-white font-semibold">
    {{ $initials }}
</div>
