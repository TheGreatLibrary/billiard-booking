@props([
    'totalAmount' => 0,
    'selectedCount' => 0,
    'wireProceed' => '',
    'disabled' => false,
    'buttonText' => 'Далее',
    'showAmount' => true,
    'showCount' => false,
    'showBorder' => true,
])

<div class="flex justify-between items-center {{ $showBorder ? 'pt-8 border-t-2 border-gray-200 dark:border-gray-700' : 'pt-4' }}">
    <!-- Левая часть: Информация -->
    <div>
        @if($showAmount && $totalAmount > 0)
            <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">Итого к оплате:</p>
            <p class="text-3xl lg:text-4xl font-bold text-green-600 dark:text-green-400">
                {{ number_format($totalAmount, 0) }} ₽
            </p>
        @elseif($showCount && $selectedCount > 0)
            <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">Выбрано:</p>
            <p class="text-xl font-bold text-blue-600 dark:text-blue-400">
                {{ $selectedCount }} {{ trans_choice('позиция|позиции|позиций', $selectedCount) }}
            </p>
        @else
            <!-- Пустой блок для выравнивания -->
            <div class="h-12"></div>
        @endif
    </div>
    
    <!-- Правая часть: Кнопка -->
    <x-auth.button 
        wire:click="{{ $wireProceed }}"
        variant="primary"
        size="lg"
        :disabled="$disabled"
        class="gap-3 min-w-[200px] justify-center"
    >
        <span>{{ $buttonText }}</span>
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
    </x-auth.button>
</div>