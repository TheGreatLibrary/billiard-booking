{{-- resources/views/components/flash-message.blade.php --}}
@props([
    'type' => 'success',
    'message' => '',
])

@php
    $config = [
        'success' => [
            'bg' => 'from-green-50 to-emerald-50 dark:from-green-950/40 dark:to-emerald-950/30',
            'border' => 'border-green-200 dark:border-green-800',
            'iconBg' => 'bg-green-100 dark:bg-green-900/40',
            'icon' => '<svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
            'text' => 'text-green-800 dark:text-green-200',
        ],
        'error' => [
            'bg' => 'from-red-50 to-pink-50 dark:from-red-950/40 dark:to-pink-950/30',
            'border' => 'border-red-200 dark:border-red-800',
            'iconBg' => 'bg-red-100 dark:bg-red-900/40',
            'icon' => '<svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
            'text' => 'text-red-800 dark:text-red-200',
        ],
        'warning' => [
            'bg' => 'from-yellow-50 to-amber-50 dark:from-yellow-950/40 dark:to-amber-950/30',
            'border' => 'border-yellow-200 dark:border-yellow-800',
            'iconBg' => 'bg-yellow-100 dark:bg-yellow-900/40',
            'icon' => '<svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.998-.833-2.732 0L4.347 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>',
            'text' => 'text-yellow-800 dark:text-yellow-200',
        ],
        'info' => [
            'bg' => 'from-blue-50 to-cyan-50 dark:from-blue-950/40 dark:to-cyan-950/30',
            'border' => 'border-blue-200 dark:border-blue-800',
            'iconBg' => 'bg-blue-100 dark:bg-blue-900/40',
            'icon' => '<svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
            'text' => 'text-blue-800 dark:text-blue-200',
        ],
    ][$type];
@endphp

@if($message)
<div {{ $attributes->merge(['class' => "mb-8 p-6 bg-gradient-to-r {$config['bg']} rounded-xl border-2 {$config['border']}"]) }}>
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 {{ $config['iconBg'] }} rounded-full flex items-center justify-center">
            {!! $config['icon'] !!}
        </div>
        <div class="flex-1">
            <p class="font-semibold {{ $config['text'] }}">{{ $message }}</p>
        </div>
    </div>
</div>
@endif