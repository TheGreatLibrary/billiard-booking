@props([
    'wireQuickSelect' => 'quickSelect',
    'wireClearSlots' => 'clearSlots',
])

<div class="mb-8 p-6 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4 flex items-center gap-2">
        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
        </svg>
        Быстрый выбор:
    </p>
    <div class="flex flex-wrap gap-3">
        <button wire:click="{{ $wireQuickSelect }}(1)" 
                class="px-6 py-3 bg-white dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 rounded-xl hover:border-blue-500 dark:hover:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-300 text-gray-900 dark:text-white font-medium hover:scale-105">
            1 час
        </button>
        <button wire:click="{{ $wireQuickSelect }}(2)" 
                class="px-6 py-3 bg-white dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 rounded-xl hover:border-blue-500 dark:hover:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-300 text-gray-900 dark:text-white font-medium hover:scale-105">
            2 часа
        </button>
        <button wire:click="{{ $wireQuickSelect }}(3)" 
                class="px-6 py-3 bg-white dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 rounded-xl hover:border-blue-500 dark:hover:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-300 text-gray-900 dark:text-white font-medium hover:scale-105">
            3 часа
        </button>
        <button wire:click="{{ $wireClearSlots }}" 
                class="px-6 py-3 bg-white dark:bg-gray-700 border-2 border-red-300 dark:border-red-500 text-red-600 dark:text-red-400 rounded-xl hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-300 font-medium hover:scale-105">
            Очистить
        </button>
    </div>
</div>