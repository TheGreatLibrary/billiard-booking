@props([
    'placeData' => [],
    'resourceId' => null,
])

@php
    $selectedResource = collect($placeData['resources'] ?? [])->firstWhere('id', $resourceId);
@endphp

<div class="mb-6 p-6 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-950/40 dark:to-indigo-950/30 rounded-xl border border-blue-200 dark:border-blue-800 flex flex-col sm:flex-row items-center justify-between gap-4">
    <!-- Левая часть: Информация о заведении -->
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/40 rounded-lg flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
        </div>
        <div>
            <p class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-1">Выбранное заведение</p>
            <p class="text-xl font-bold text-gray-900 dark:text-white">
                📍 {{ $placeData['place']['name'] ?? 'Не выбрано' }}
            </p>
        </div>
    </div>
    
    <!-- Правая часть: Информация о столе -->
    @if($resourceId && $selectedResource)
        <div class="text-right">
            <p class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-1">Выбранный стол</p>
            <div class="flex items-center gap-2 justify-end">
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/40 rounded-lg flex items-center justify-center">
                    <span class="text-lg">🎱</span>
                </div>
                <p class="text-xl font-bold text-green-600 dark:text-green-400">
                    {{ $selectedResource['code'] ?? 'N/A' }}
                </p>
            </div>
        </div>
    @else
        <div class="text-right">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Стол не выбран</p>
            <p class="text-lg text-gray-500 dark:text-gray-500">Выберите стол из плана ниже</p>
        </div>
    @endif
</div>