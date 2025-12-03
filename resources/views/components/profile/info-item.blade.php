@props([
    'label',
    'value',
    'icon' => 'info',
    'color' => 'blue',
    'highlight' => false,
])

@php
    $colors = [
        'blue' => ['bg' => 'bg-blue-100 dark:bg-blue-900/40', 'text' => 'text-blue-600 dark:text-blue-400'],
        'green' => ['bg' => 'bg-green-100 dark:bg-green-900/40', 'text' => 'text-green-600 dark:text-green-400'],
        'purple' => ['bg' => 'bg-purple-100 dark:bg-purple-900/40', 'text' => 'text-purple-600 dark:text-purple-400'],
    ][$color];
    
    $icons = [
        'calendar' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
        'shield' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>',
    ];
@endphp

<div class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
    <div class="w-12 h-12 {{ $colors['bg'] }} rounded-lg flex items-center justify-center">
        <svg class="w-6 h-6 {{ $colors['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            {!! $icons[$icon] ?? '' !!}
        </svg>
    </div>
    <div>
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $label }}</p>
        <p class="text-lg font-semibold @if($highlight) {{ $colors['text'] }} @else text-gray-900 dark:text-white @endif">
            {{ $value }}
        </p>
    </div>
</div>