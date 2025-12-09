@props([
    'equipment' => [],
])

@php
    $isAvailable = ($equipment['available_qty'] ?? 0) > 0;
    $price = ($equipment['price'] ?? 0);
    $resourceId = $equipment['resource_id'] ?? 0;
    $wireAddEquipment = $attributes->get('wire-add-equipment', 'addEquipment');
@endphp

<div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-6 border-2 border-transparent 
            hover:border-amber-300 dark:hover:border-amber-700 transition-all duration-300 
            {{ $isAvailable ? 'hover:shadow-lg' : 'opacity-70' }}">
    
    <!-- Заголовок и цена -->
    <div class="flex justify-between items-start mb-4">
        <div class="flex-1">
            <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-1">
                {{ $equipment['name'] ?? 'Оборудование' }}
            </h3>
            @if(!empty($equipment['code']))
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Код: {{ $equipment['code'] }}
                </p>
            @endif
        </div>
        <div class="text-right">
            <div class="text-xl font-bold text-green-600 dark:text-green-400">
                {{ number_format($price, 0) }} ₽
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-400">
                за единицу
            </div>
        </div>
    </div>
    
    <!-- Доступность -->
    <div class="mb-4 p-3 bg-white dark:bg-gray-900 rounded-lg text-center">
        <span class="text-sm text-gray-700 dark:text-gray-300">
            Доступно: 
            <strong class="{{ $isAvailable ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                {{ $equipment['available_qty'] ?? 0 }}
            </strong> 
            из {{ $equipment['total_qty'] ?? 0 }}
        </span>
    </div>
    
    <!-- Кнопка добавления -->
@if($isAvailable)
    <x-auth.button 
        wire:click="{{ $wireAddEquipment }}({{ $resourceId }})"
        variant="primary"
        size="full"
        class="w-full"
    >
        + Добавить в заказ
    </x-auth.button>
@else
    <x-auth.button 
        disabled
        variant="secondary"
        size="full"
        class="w-full cursor-not-allowed opacity-70"
    >
        Недоступно
    </x-auth.button>
@endif
</div>