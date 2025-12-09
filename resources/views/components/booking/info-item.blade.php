@props([
    'icon' => 'location-marker',
    'label' => '',
    'value' => '',
    'color' => 'blue',
])

@php
    $colors = [
        'blue' => 'from-blue-50 to-indigo-50 dark:from-blue-950/40 dark:to-indigo-950/30 border-blue-200 dark:border-blue-800',
        'green' => 'from-green-50 to-emerald-50 dark:from-green-950/40 dark:to-emerald-950/30 border-green-200 dark:border-green-800',
        'amber' => 'from-amber-50 to-orange-50 dark:from-amber-950/40 dark:to-orange-950/30 border-amber-200 dark:border-amber-800',
    ];
    
    $textColors = [
        'blue' => 'text-blue-800 dark:text-blue-200',
        'green' => 'text-green-800 dark:text-green-200',
        'amber' => 'text-amber-800 dark:text-amber-200',
    ];
    
    $icons = [
        'location-marker' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z',
        'table' => 'M4 6h16M4 10h16M4 14h16M4 18h16',
        'clock' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        'user' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
        'info' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    ];
    
    $selectedColor = $colors[$color] ?? $colors['blue'];
    $selectedTextColor = $textColors[$color] ?? $textColors['blue'];
    $selectedIcon = $icons[$icon] ?? $icons['info'];
@endphp

<div {{ $attributes->merge(['class' => "p-6 bg-gradient-to-r rounded-xl border {$selectedColor}"]) }}>
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-white dark:bg-gray-800 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 {{ $selectedTextColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $selectedIcon }}"></path>
            </svg>
        </div>
        <div>
            <p class="text-sm font-medium mb-1 {{ $selectedTextColor }}">{{ $label }}</p>
            <p class="font-bold text-lg text-gray-900 dark:text-white">{{ $value }}</p>
        </div>
    </div>
</div>