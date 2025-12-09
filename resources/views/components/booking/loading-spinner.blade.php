@props([
    'size' => 'md',
    'color' => 'amber',
])

@php
    $sizes = [
        'sm' => 'w-4 h-4',
        'md' => 'w-6 h-6',
        'lg' => 'w-8 h-8',
        'xl' => 'w-10 h-10',
    ];
    
    $colors = [
        'amber' => 'text-amber-600 dark:text-amber-400',
        'white' => 'text-white',
        'gray' => 'text-gray-600 dark:text-gray-400',
    ];
@endphp

<svg {{ $attributes->merge(['class' => "animate-spin {$sizes[$size]} {$colors[$color]}"]) }} 
     fill="none" 
     stroke="currentColor" 
     viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
</svg>