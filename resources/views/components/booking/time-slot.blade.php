@props([
    'time' => '',
    'price' => 0,
    'available' => true,
    'selected' => false,
    'wireToggleSlot' => 'toggleSlot',
])

@php
    $formattedPrice = number_format($price, 0);
@endphp

<button 
    wire:click="{{ $wireToggleSlot }}('{{ $time }}')"
    @disabled(!$available)
    type="button"
    class="flex flex-col items-center justify-center p-4 rounded-xl border-2 transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 dark:focus:ring-offset-gray-900 min-w-[120px]
           {{ $selected
              ? 'border-amber-500 dark:border-amber-400 bg-gradient-to-br from-amber-500 to-orange-500 dark:from-amber-600 dark:to-orange-600 text-white shadow-2xl scale-105' 
              : ($available 
                 ? 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-amber-400 dark:hover:border-amber-500 hover:bg-amber-50 dark:hover:bg-amber-900/20 text-gray-900 dark:text-white hover:shadow-xl' 
                 : 'border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-700 opacity-50 cursor-not-allowed text-gray-500 dark:text-gray-400') }}">
    
    <!-- Время -->
    <div class="font-bold text-2xl mb-2 {{ $selected ? 'text-white' : '' }}">
        {{ $time }}
    </div>
    
    <!-- Цена или статус -->
    @if($available)
        <div class="text-lg font-semibold {{ $selected ? 'text-amber-100' : 'text-gray-600 dark:text-gray-300' }}">
            {{ $formattedPrice }} ₽
        </div>
    @else
        <div class="text-sm font-medium text-red-600 dark:text-red-400 bg-red-100 dark:bg-red-900/30 px-3 py-1 rounded-full">
            Занято
        </div>
    @endif
    
    <!-- Индикатор выбора -->
    @if($selected)
        <div class="absolute -top-2 -right-2 w-6 h-6 bg-green-500 dark:bg-green-400 rounded-full flex items-center justify-center">
            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
            </svg>
        </div>
    @endif
</button>