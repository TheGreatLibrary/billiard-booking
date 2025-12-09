@props([
    'amount' => 0,
    'size' => 'md',
    'showLabel' => true,
])

@php
    $sizes = [
        'sm' => 'text-2xl',
        'md' => 'text-3xl',
        'lg' => 'text-4xl',
    ];
    
    $formattedAmount = number_format($amount, 0);
@endphp

<div {{ $attributes->merge(['class' => 'text-right']) }}>
    @if($showLabel)
        <p class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">Итого к оплате:</p>
    @endif
    <p class="{{ $sizes[$size] }} font-bold text-green-600 dark:text-green-400">
        {{ $formattedAmount }} ₽
    </p>
</div>