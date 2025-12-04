@props([
    'title',
    'value',
    'description',
    'color' => 'blue',
    'icon' => null,
])

@php
    $glassColors = [
        'blue' => [
            'bg' => 'bg-blue-500/10',
            'border' => 'border-blue-500/20',
            'backdrop' => 'backdrop-blur-lg',
            'gradient' => 'from-blue-500 to-cyan-400',
            'text' => 'text-blue-600 dark:text-blue-400',
        ],
        'green' => [
            'bg' => 'bg-emerald-500/10',
            'border' => 'border-emerald-500/20',
            'backdrop' => 'backdrop-blur-lg',
            'gradient' => 'from-emerald-500 to-teal-400',
            'text' => 'text-emerald-600 dark:text-emerald-400',
        ],
        'purple' => [
            'bg' => 'bg-purple-500/10',
            'border' => 'border-purple-500/20',
            'backdrop' => 'backdrop-blur-lg',
            'gradient' => 'from-purple-500 to-violet-400',
            'text' => 'text-purple-600 dark:text-purple-400',
        ],
        'orange' => [
            'bg' => 'bg-orange-500/10',
            'border' => 'border-orange-500/20',
            'backdrop' => 'backdrop-blur-lg',
            'gradient' => 'from-orange-500 to-amber-400',
            'text' => 'text-orange-600 dark:text-orange-400',
        ],
    ];
    
    $colorData = $glassColors[$color] ?? $glassColors['blue'];
@endphp

<div class="bg-white/80 dark:bg-gray-900/80 {{ $colorData['backdrop'] }} rounded-2xl p-6 border {{ $colorData['border'] }} shadow-xl transition-all hover:shadow-2xl duration-300">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ $title }}</p>
            <p class="text-3xl font-bold mt-2 bg-gradient-to-r {{ $colorData['gradient'] }} bg-clip-text text-transparent">
                {{ $value }}
            </p>
            <p class="text-sm {{ $colorData['text'] }} mt-1">{{ $description }}</p>
        </div>
        
        @if($icon)
            <div class="p-4 rounded-xl {{ $colorData['bg'] }} {{ $colorData['backdrop'] }} border {{ $colorData['border'] }}">
                <div class="bg-gradient-to-r {{ $colorData['gradient'] }} bg-clip-text text-transparent">
                    {!! $icon !!}
                </div>
            </div>
        @endif
    </div>
</div>