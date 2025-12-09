@props([
    'method' => 'card',
    'title' => '',
    'description' => '',
    'icon' => 'credit-card',
    'wirePayBooking' => 'payBooking',
    'color' => 'blue',
    'loadingText' => null, // Текст во время загрузки
])

@php
    $colors = [
        'blue' => [
            'bg' => 'bg-blue-100 dark:bg-blue-900/40',
            'text' => 'text-blue-600 dark:text-blue-400',
            'border' => 'border-blue-500 dark:border-blue-400',
            'hover' => 'hover:border-blue-600 dark:hover:border-blue-300',
        ],
        'green' => [
            'bg' => 'bg-green-100 dark:bg-green-900/40',
            'text' => 'text-green-600 dark:text-green-400',
            'border' => 'border-green-500 dark:border-green-400',
            'hover' => 'hover:border-green-600 dark:hover:border-green-300',
        ],
        'amber' => [
            'bg' => 'bg-amber-100 dark:bg-amber-900/40',
            'text' => 'text-amber-600 dark:text-amber-400',
            'border' => 'border-amber-500 dark:border-amber-400',
            'hover' => 'hover:border-amber-600 dark:hover:border-amber-300',
        ],
        'purple' => [
            'bg' => 'bg-purple-100 dark:bg-purple-900/40',
            'text' => 'text-purple-600 dark:text-purple-400',
            'border' => 'border-purple-500 dark:border-purple-400',
            'hover' => 'hover:border-purple-600 dark:hover:border-purple-300',
        ],
    ];
    
    $colorConfig = $colors[$color] ?? $colors['blue'];
    
    $icons = [
        'credit-card' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
        'globe' => 'M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9m0 0h9m-9 0H3',
        'cash' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
        'qr' => 'M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z',
    ];
    
    $iconPath = $icons[$icon] ?? $icons['credit-card'];
    $loadingId = 'payment-loading-' . uniqid();
@endphp

<div 
    wire:loading.class="opacity-70 cursor-not-allowed"
    wire:target="{{ $wirePayBooking }}"
    class="w-full"
>
    <button 
        wire:click="{{ $wirePayBooking }}('{{ $method }}')"
        wire:loading.attr="disabled"
        wire:loading.class="!scale-100"
        class="w-full p-6 border-2 {{ $colorConfig['border'] }} rounded-xl {{ $colorConfig['hover'] }} 
               hover:bg-white dark:hover:bg-gray-700 transition-all duration-300 text-left 
               bg-white dark:bg-gray-800 group hover:scale-105 focus:outline-none focus:ring-2 
               focus:ring-{{ $color }}-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
        {{ $attributes }}
    >
        <div class="flex items-center gap-4">
            <!-- Иконка -->
            <div class="relative w-12 h-12 {{ $colorConfig['bg'] }} rounded-lg flex items-center justify-center 
                      group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6 {{ $colorConfig['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconPath }}"></path>
                </svg>
                
                <!-- Индикатор загрузки -->
                <div wire:loading wire:target="{{ $wirePayBooking }}"
                     class="absolute inset-0 flex items-center justify-center bg-white/80 dark:bg-gray-800/80 rounded-lg">
                    <svg class="w-5 h-5 {{ $colorConfig['text'] }} animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </div>
            </div>
            
            <!-- Контент -->
            <div class="flex-1">
                <p class="font-semibold text-lg text-gray-900 dark:text-white">
                    <span wire:loading.remove wire:target="{{ $wirePayBooking }}">
                        {{ $title }}
                    </span>
                    <span wire:loading wire:target="{{ $wirePayBooking }}">
                        {{ $loadingText ?? 'Обработка...' }}
                    </span>
                </p>
                <p class="text-gray-600 dark:text-gray-300 text-sm">
                    <span wire:loading.remove wire:target="{{ $wirePayBooking }}">
                        {{ $description }}
                    </span>
                    <span wire:loading wire:target="{{ $wirePayBooking }}">
                        Пожалуйста, подождите...
                    </span>
                </p>
            </div>
            
            <!-- Стрелка -->
            <div class="flex-shrink-0">
                <svg class="w-5 h-5 text-gray-400 group-hover:{{ $colorConfig['text'] }} transition-colors" 
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
        </div>
    </button>
</div>