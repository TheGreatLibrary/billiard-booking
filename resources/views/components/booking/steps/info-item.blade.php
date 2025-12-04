@props([
    'icon' => 'info',
    'label' => '',
    'value' => '',
    'color' => 'blue',
    'size' => 'md',
])

@php
    $colors = [
        'blue' => 'bg-blue-50 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200 border-blue-200 dark:border-blue-800',
        'green' => 'bg-green-50 dark:bg-green-900/30 text-green-800 dark:text-green-200 border-green-200 dark:border-green-800',
        'amber' => 'bg-amber-50 dark:bg-amber-900/30 text-amber-800 dark:text-amber-200 border-amber-200 dark:border-amber-800',
        'purple' => 'bg-purple-50 dark:bg-purple-900/30 text-purple-800 dark:text-purple-200 border-purple-200 dark:border-purple-800',
    ];
    
    $sizes = [
        'sm' => 'p-4',
        'md' => 'p-5',
        'lg' => 'p-6',
    ];
    
    $icons = [
        'location-marker' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z',
        'table' => 'M4 6h16M4 10h16M4 14h16M4 18h16',
        'clock' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        'user' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
        'info' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    ];
@endphp

<div {{ $attributes->merge(['class' => "rounded-xl border {$colors[$color]} {$sizes[$size]} transition-all duration-300 hover:scale-[1.02] hover:shadow-lg"]) }}>
    <div class="flex items-start gap-4">
        <div class="flex-shrink-0">
            <div class="w-10 h-10 rounded-lg bg-white dark:bg-gray-800 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icons[$icon] ?? $icons['info'] }}"></path>
                </svg>
            </div>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-medium opacity-80 mb-1">{{ $label }}</p>
            <p class="font-bold text-lg truncate">{{ $value }}</p>
        </div>
    </div>
</div>