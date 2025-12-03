@props([
    'title',
    'description',
    'href',
    'color' => 'blue',
    'icon' => null,
])

@php
    $colorMap = [
        'blue' => 'bg-blue-600',
        'green' => 'bg-green-600',
        'red' => 'bg-red-600',
        'yellow' => 'bg-yellow-600',
        'purple' => 'bg-purple-600',
        'pink' => 'bg-pink-600',
        'indigo' => 'bg-indigo-600',
        'gray' => 'bg-gray-600 dark:bg-gray-700',
        'amber' => 'bg-amber-600',
    ];
    
    $bgClass = $colorMap[$color] ?? $colorMap['blue'];
@endphp

<a href="{{ $href }}" 
   class="block bg-white dark:bg-gray-800 rounded-xl shadow p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow">
    <div class="flex items-start">
        @if($icon)
            <div class="p-3 rounded-lg {{ $bgClass }} mr-4">
                {!! $icon !!}
            </div>
        @endif
        
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $title }}</h3>
            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $description }}</p>
        </div>
    </div>
</a>