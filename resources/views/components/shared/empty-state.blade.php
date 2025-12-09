@props([
    'title',
    'description',
    'href' => null,
    'buttonText' => 'Создать',
])

<div class="text-center py-12">
    <!-- Иконка -->
    <div class="mx-auto w-24 h-24 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-6">
        <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
    </div>

    <!-- Текст -->
    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">{{ $title }}</h3>
    <p class="text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto">{{ $description }}</p>

    <!-- Кнопка -->
    @if($href)
        <a href="{{ $href }}" 
           class="inline-flex items-center px-6 py-3 bg-amber-600 hover:bg-amber-700 text-white font-semibold rounded-lg transition-colors duration-200">
            {{ $buttonText }}
        </a>
    @endif
</div>