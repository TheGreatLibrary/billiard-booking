@props(['title', 'color' => 'blue'])

@php
    $colors = [
        'blue' => ['bg' => 'bg-blue-100 dark:bg-blue-900/40', 'text' => 'text-blue-600 dark:text-blue-400'],
        'green' => ['bg' => 'bg-green-100 dark:bg-green-900/40', 'text' => 'text-green-600 dark:text-green-400'],
        'purple' => ['bg' => 'bg-purple-100 dark:bg-purple-900/40', 'text' => 'text-purple-600 dark:text-purple-400'],
    ][$color];
@endphp

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 rounded-xl shadow-lg dark:shadow-gray-900/30 p-8 border border-gray-200 dark:border-gray-700']) }}>
    <div class="flex items-center gap-3 mb-6">
        <div class="w-12 h-12 {{ $colors['bg'] }} rounded-lg flex items-center justify-center">
            <x-profile.icon :name="$color" class="w-6 h-6 {{ $colors['text'] }}" />
        </div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $title }}</h2>
    </div>
    
    {{ $slot }}
</div>