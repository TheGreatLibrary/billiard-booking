{{-- components/auth/button.blade.php --}}
@props([
    'type' => 'submit',
    'variant' => 'primary',
    'size' => 'default',
    'href' => null, // Добавляем поддержку ссылок
])

@php
    $variantClasses = match($variant) {
        'primary' => 'bg-gradient-to-r from-amber-600 to-amber-700 hover:from-amber-700 hover:to-amber-800 
                      text-white shadow-lg hover:shadow-xl',
        'secondary' => 'bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 
                        text-gray-800 dark:text-gray-200',
        'success' => 'bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 
                      text-white shadow-lg hover:shadow-xl',
        'ghost' => 'bg-transparent border border-gray-300 dark:border-gray-600 
                    hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300',
        'danger' => 'bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 
                     text-white shadow-lg hover:shadow-xl',
        default => 'bg-amber-600 hover:bg-amber-700 text-white',
    };
    
    $sizeClasses = match($size) {
        'sm' => 'px-4 py-2 text-sm',
        'lg' => 'px-8 py-3 text-lg',
        'xl' => 'px-10 py-4 text-xl',
        'full' => 'w-full py-3 px-4',
        default => 'px-6 py-3',
    };
@endphp

@if($href)
    <a 
        href="{{ $href }}"
        {{ $attributes->merge(['class' => "inline-flex items-center justify-center rounded-lg font-medium 
                                           transition-all duration-300 transform hover:-translate-y-0.5 
                                           active:translate-y-0 focus:outline-none focus:ring-2 
                                           focus:ring-amber-500 focus:ring-offset-2 
                                           dark:focus:ring-offset-gray-800 {$variantClasses} {$sizeClasses}"]) }}
    >
        {{ $slot }}
    </a>
@else
    <button
        type="{{ $type }}"
        {{ $attributes->merge(['class' => "inline-flex items-center justify-center rounded-lg font-medium 
                                           transition-all duration-300 transform hover:-translate-y-0.5 
                                           active:translate-y-0 focus:outline-none focus:ring-2 
                                           focus:ring-amber-500 focus:ring-offset-2 
                                           dark:focus:ring-offset-gray-800 {$variantClasses} {$sizeClasses}"]) }}
    >
        {{ $slot }}
    </button>
@endif