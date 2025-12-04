@props([
    'type' => 'submit',
    'variant' => 'primary',
])

@php
    $variantClasses = match($variant) {
        'primary' => 'bg-gradient-to-r from-amber-600 to-amber-700 hover:from-amber-700 hover:to-amber-800 
                      text-white shadow-lg hover:shadow-xl',
        'secondary' => 'bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 
                        text-gray-800 dark:text-gray-200',
        'ghost' => 'bg-transparent border border-gray-300 dark:border-gray-600 
                    hover:bg-gray-100 dark:hover:bg-gray-700',
        default => 'bg-amber-600 hover:bg-amber-700 text-white',
    };
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->merge(['class' => "px-6 py-3 rounded-lg font-medium transition-all duration-300 
                                       transform hover:-translate-y-0.5 active:translate-y-0 
                                       focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 
                                       dark:focus:ring-offset-gray-800 {$variantClasses}"]) }}
>
    {{ $slot }}
</button>