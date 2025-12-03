@props([
    'place' => null,
    'wireSelectPlace' => 'selectPlace',
])

@php
    $placeData = $place?->toArray() ?? [];
@endphp

<button 
    wire:click="{{ $wireSelectPlace }}({{ $placeData['id'] ?? 0 }})"
    type="button"
    class="group relative p-6 border-2 border-gray-200 dark:border-gray-700 rounded-xl hover:border-amber-500 dark:hover:border-amber-400 hover:bg-gradient-to-br hover:from-amber-50/50 hover:to-orange-50/50 dark:hover:from-amber-950/20 dark:hover:to-orange-950/20 transition-all duration-300 text-left bg-white dark:bg-gray-700 shadow-sm hover:shadow-2xl dark:hover:shadow-amber-900/30 hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-amber-500 dark:focus:ring-amber-400 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
    
    <!-- Градиентный акцент -->
    <div class="absolute inset-0 bg-gradient-to-br from-amber-500/0 to-orange-600/0 group-hover:from-amber-500/5 group-hover:to-orange-600/5 dark:group-hover:from-amber-500/10 dark:group-hover:to-orange-600/10 rounded-xl transition-all duration-300"></div>
    
    <!-- Иконка -->
    <div class="relative z-10">
        <div class="w-14 h-14 bg-gradient-to-br from-amber-100 to-orange-100 dark:from-amber-900/30 dark:to-orange-900/30 rounded-xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform duration-300">
            <svg class="w-7 h-7 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
        </div>
        
        <!-- Название -->
        <h3 class="font-bold text-xl text-gray-900 dark:text-white mb-2 group-hover:text-amber-700 dark:group-hover:text-amber-300 transition-colors">
            {{ $placeData['name'] ?? 'Название заведения' }}
        </h3>
        
        <!-- Адрес -->
        <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed mb-4">
            📍 {{ $placeData['address'] ?? 'Адрес не указан' }}
        </p>
        
        <!-- Дополнительная информация -->
        <div class="flex flex-wrap gap-2 mt-4">
            @if(isset($placeData['tables_count']))
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200">
                    🎱 {{ $placeData['tables_count'] }} столов
                </span>
            @endif
            
            @if(isset($placeData['price_per_hour']))
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">
                    💰 от {{ number_format($placeData['price_per_hour'] / 100, 0) }} ₽/час
                </span>
            @endif
        </div>
    </div>
</button>