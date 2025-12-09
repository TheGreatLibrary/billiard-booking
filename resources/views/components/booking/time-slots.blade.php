@props([
    'availableSlots' => [],
    'selectedSlots' => [],
    'wireToggleSlot' => 'toggleSlot',
])

@php
    $selectedSlots = $selectedSlots ?? [];
    $availableSlots = $availableSlots ?? [];
@endphp

<div class="mb-8">
    <div class="flex items-center justify-between mb-6">
        <label class="block font-semibold text-lg dark:text-white flex items-center gap-2">
            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Выберите время
        </label>
        @if(count($selectedSlots) > 0)
            <span class="text-sm font-medium text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-900/30 px-3 py-1 rounded-full">
                Выбрано: {{ count($selectedSlots) }} {{ trans_choice('час|часа|часов', count($selectedSlots)) }}
            </span>
        @endif
    </div>

    {{-- Слоты в виде горизонтальной ленты --}}
    <div class="relative">
        <div class="overflow-x-auto pb-6 scrollbar-hide">
            <div class="flex gap-4 min-w-max px-2">
                @foreach($availableSlots as $time => $slot)
                    @php
                        $slotData = is_array($slot) ? $slot : (method_exists($slot, 'toArray') ? $slot->toArray() : []);
                        $isAvailable = $slotData['available'] ?? false;
                        $price = $slotData['price'] ?? 0;
                        $isSelected = in_array($time, $selectedSlots);
                    @endphp
                    
                    <button wire:click="{{ $wireToggleSlot }}('{{ $time }}')"
                            @disabled(!$isAvailable)
                            class="flex-shrink-0 p-6 rounded-xl border-2 text-center transition-all duration-300 group
                                   min-w-[140px] hover:scale-105 transform
                                   {{ $isSelected 
                                      ? 'border-blue-500 dark:border-blue-400 bg-blue-500 dark:bg-blue-600 text-white shadow-2xl scale-105' 
                                      : ($isAvailable 
                                         ? 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 hover:border-blue-400 dark:hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:shadow-xl text-gray-900 dark:text-white' 
                                         : 'border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-700 opacity-50 cursor-not-allowed text-gray-500 dark:text-gray-400') }}">
                        <div class="font-bold text-2xl mb-2">{{ $time }}</div>
                        @if($isAvailable)
                            <div class="text-lg font-semibold {{ $isSelected ? 'text-blue-100' : 'text-gray-600 dark:text-gray-300' }}">
                                {{ number_format($price, 0) }} ₽
                            </div>
                        @else
                            <div class="text-sm font-medium text-red-600 dark:text-red-400 bg-red-100 dark:bg-red-900/30 px-2 py-1 rounded">
                                Занято
                            </div>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>
        
        {{-- Градиенты для индикации прокрутки --}}
        <div class="absolute left-0 top-0 bottom-6 w-12 bg-gradient-to-r from-white dark:from-gray-900 to-transparent pointer-events-none"></div>
        <div class="absolute right-0 top-0 bottom-6 w-12 bg-gradient-to-l from-white dark:from-gray-900 to-transparent pointer-events-none"></div>
    </div>

    <p class="text-sm text-gray-500 dark:text-gray-400 mt-4 flex items-center gap-2 justify-center">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"></path>
        </svg>
        Прокрутите влево/вправо для выбора времени
    </p>
</div>