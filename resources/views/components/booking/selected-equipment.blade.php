@props([
    'equipment' => [],
    'wireUpdateEquipmentQty' => 'updateEquipmentQty',
    'wireRemoveEquipment' => 'removeEquipment',
])

<div {{ $attributes->merge(['class' => 'space-y-4']) }}>
    <h3 class="font-semibold text-xl text-gray-900 dark:text-white mb-4 flex items-center gap-2">
        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        Выбранное оборудование
    </h3>
    
    @foreach($equipment as $index => $item)
        @php
            $itemData = is_array($item) ? $item : $item->toArray();
        @endphp
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between bg-gray-50 dark:bg-gray-800 p-6 rounded-xl border-2 border-green-200 dark:border-green-800 gap-4 hover:bg-white dark:hover:bg-gray-700 transition-colors">
            <!-- Левая часть: Информация -->
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/40 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-lg text-gray-900 dark:text-white">{{ $itemData['name'] ?? '' }}</p>
                    <p class="text-green-600 dark:text-green-400 font-semibold">
                        {{ number_format(($itemData['price'] ?? 0) / 100, 0) }} ₽/шт
                    </p>
                </div>
            </div>
            
            <!-- Правая часть: Управление -->
            <div class="flex items-center gap-4">
                <!-- Количество -->
                <div class="flex items-center gap-2">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-300 whitespace-nowrap">Кол-во:</span>
                    <div class="flex items-center gap-1">
                        <button 
                            wire:click="{{ $wireUpdateEquipmentQty }}({{ $index }}, {{ max(1, ($itemData['qty'] ?? 1) - 1) }})"
                            class="w-8 h-8 flex items-center justify-center border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            {{ ($itemData['qty'] ?? 1) <= 1 ? 'disabled' : '' }}
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                            </svg>
                        </button>
                        
                        <input 
                            type="number" 
                            value="{{ $itemData['qty'] ?? 1 }}"
                            wire:change.debounce.500ms="{{ $wireUpdateEquipmentQty }}({{ $index }}, $event.target.value)"
                            min="1"
                            max="99"
                            class="w-16 border-2 border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-center bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-amber-500 dark:focus:border-amber-400 focus:ring-2 focus:ring-amber-200 dark:focus:ring-amber-800/30 transition-colors"
                        >
                        
                        <button 
                            wire:click="{{ $wireUpdateEquipmentQty }}({{ $index }}, {{ ($itemData['qty'] ?? 1) + 1 }})"
                            class="w-8 h-8 flex items-center justify-center border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Итого и удаление -->
                <div class="flex items-center gap-4">
                    <p class="text-lg font-bold text-gray-900 dark:text-white whitespace-nowrap">
                        {{ number_format((($itemData['price'] ?? 0) * ($itemData['qty'] ?? 1)) / 100, 0) }} ₽
                    </p>
                    
                    <button 
                        wire:click="{{ $wireRemoveEquipment }}({{ $index }})"
                        class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                        title="Удалить"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    @endforeach
</div>