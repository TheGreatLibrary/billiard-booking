@props([
    'equipment' => [],
])

@php
    $hasEquipment = count($equipment) > 0;
    $wireUpdateEquipmentQty = $attributes->get('wire-update-equipment-qty', 'updateEquipmentQty');
    $wireRemoveEquipment = $attributes->get('wire-remove-equipment', 'removeEquipment');
@endphp

@if($hasEquipment)
    <div {{ $attributes->merge(['class' => 'bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-900 rounded-xl p-6 border-2 border-blue-200 dark:border-gray-700']) }}>
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
            </svg>
            Выбранное оборудование
        </h3>
        
        <div class="space-y-4">
            @foreach($equipment as $index => $item)
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border-2 border-blue-100 dark:border-gray-700">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <!-- Информация -->
                        <div class="flex-1">
                            <h4 class="font-bold text-lg text-gray-900 dark:text-white">
                                {{ $item['name'] ?? 'Оборудование' }}
                            </h4>
                            <div class="mt-2 space-y-1">
                                @php
                                    $pricePerUnit = ($item['price'] ?? 0);
                                    $quantity = $item['qty'] ?? 1;
                                    $total = $pricePerUnit * $quantity;
                                @endphp
                                <p class="text-sm text-gray-600 dark:text-gray-300">
                                    {{ number_format($pricePerUnit, 0) }} ₽ × {{ $quantity }} = 
                                    <span class="font-bold text-green-600 dark:text-green-400">
                                        {{ number_format($total, 0) }} ₽
                                    </span>
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Максимально доступно: {{ $item['max_qty'] ?? 1 }} единиц
                                </p>
                            </div>
                        </div>
                        
                        <!-- Управление количеством -->
                        <div class="flex items-center gap-3">
                            <div class="flex items-center border-2 border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden">
<button
    wire:click="{{ $wireUpdateEquipmentQty }}({{ $index }}, {{ max(1, ($item['qty'] ?? 1) - 1) }})"
    class="px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 
           transition text-gray-700 dark:text-gray-300 rounded-l-lg">
    −
</button>

<button
    wire:click="{{ $wireUpdateEquipmentQty }}({{ $index }}, {{ min(($item['max_qty'] ?? 1), ($item['qty'] ?? 1) + 1) }})"
    @disabled(($item['qty'] ?? 1) >= ($item['max_qty'] ?? 1))
    class="px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 
           transition text-gray-700 dark:text-gray-300 rounded-r-lg
           {{ ($item['qty'] ?? 1) >= ($item['max_qty'] ?? 1) ? 'opacity-50 cursor-not-allowed' : '' }}">
    +
</button>
                            </div>
                            
                            <!-- Кнопка удаления -->
                            <button
                                wire:click="{{ $wireRemoveEquipment }}({{ $index }})"
                                class="p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif