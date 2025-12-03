@props([
    'title' => '',
    'subtitle' => '',
    'step' => null,
    'wireGoBack' => null,
])

<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-8">
    <div>
        <!-- Номер шага -->
        @if($step)
            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-gradient-to-r from-amber-500 to-orange-500 dark:from-amber-600 dark:to-orange-600 text-white mb-3">
                Шаг {{ $step }}
            </span>
        @endif
        
        <!-- Заголовок -->
        <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white mb-2">
            {{ $title }}
        </h2>
        
        <!-- Подзаголовок -->
        <p class="text-lg text-gray-600 dark:text-gray-400">
            {{ $subtitle }}
        </p>
    </div>
    
    <!-- Кнопка назад -->
    @if($wireGoBack)
        <x-auth.button 
            wire:click="{{ $wireGoBack }}"
            variant="secondary"
            size="md"
            class="self-start sm:self-center"
        >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Назад
        </x-auth.button>
    @endif
</div>