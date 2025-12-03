@props([
    'equipment' => [],
    'wireAddEquipment' => 'addEquipment',
])

@php
    $equipmentData = is_array($equipment) ? $equipment : $equipment->toArray();
@endphp

<div class="group relative border-2 border-gray-200 dark:border-gray-700 rounded-xl p-6 bg-white dark:bg-gray-800 hover:border-amber-500 dark:hover:border-amber-400 transition-all duration-300 hover:shadow-xl">
    <!-- Градиентный эффект при наведении -->
    <div class="absolute inset-0 bg-gradient-to-br from-amber-500/0 to-orange-600/0 group-hover:from-amber-500/5 group-hover:to-orange-600/5 dark:group-hover:from-amber-500/10 dark:group-hover:to-orange-600/10 rounded-xl transition-all duration-300"></div>
    
    <div class="relative">
        <!-- Иконка -->
        <div class="w-12 h-12 bg-gradient-to-br from-amber-100 to-orange-100 dark:from-amber-900/30 dark:to-orange-900/30 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
            <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
        </div>
        
        <!-- Название -->
        <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-2 group-hover:text-amber-700 dark:group-hover:text-amber-300">
            {{ $equipmentData['name'] ?? 'Оборудование' }}
        </h3>
        
        <!-- Описание -->
        <p class="text-gray-600 dark:text-gray-300 text-sm mb-4 line-clamp-2">
            {{ $equipmentData['description'] ?? 'Дополнительное оборудование' }}
        </p>
        
        <!-- Цена и кнопка -->
        <div class="flex items-center justify-between">
            <div>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                    {{ number_format(($equipmentData['price'] ?? 0) / 100, 0) }} ₽
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    за единицу
                </p>
            </div>
            
            <x-auth.button 
                wire:click="{{ $wireAddEquipment }}({{ $equipmentData['id'] ?? 0 }})"
                variant="primary"
                size="sm"
                class="group/btn"
            >
                <span class="group-hover/btn:scale-105 transition-transform">Добавить</span>
                <svg class="w-4 h-4 ml-2 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
            </x-auth.button>
        </div>
    </div>
</div>