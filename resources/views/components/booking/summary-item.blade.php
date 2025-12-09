@props([
    'label' => '',
    'value' => '',
    'icon' => null,
])

@php
    $label = $label ?? '';
    $value = $value ?? '';
    
    $icons = [
        'location-marker' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z',
        'table' => 'M4 6h16M4 10h16M4 14h16M4 18h16',
        'clock' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        'calendar' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
        'user' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
        'chat' => 'M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z',
    ];
    
    $iconPath = $icons[$icon] ?? null;
@endphp

<div class="py-3 border-b border-gray-200 dark:border-gray-600">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            @if($icon && $iconPath)
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconPath }}"></path>
                </svg>
            @endif
            <span class="text-gray-600 dark:text-gray-300 font-medium">{{ $label }}</span>
        </div>
        <span class="font-semibold text-gray-900 dark:text-white">{{ $value }}</span>
    </div>
</div>