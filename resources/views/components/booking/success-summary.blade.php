@props([
    'booking' => null,
    'isPaid' => false,
])

@php
    $bookingData = $booking?->toArray() ?? [];
@endphp

<div {{ $attributes->merge(['class' => 'bg-gray-50 dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700']) }}>
    <h3 class="font-bold text-xl text-gray-900 dark:text-white mb-6 flex items-center gap-2">
        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
        </svg>
        Детали бронирования:
    </h3>
    
    <div class="space-y-4">
        @if($booking?->place?->name ?? false)
            <div class="flex justify-between items-center py-2">
                <span class="text-gray-600 dark:text-gray-300">Заведение:</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ $booking->place->name }}</span>
            </div>
        @endif
        
        @if($booking?->resource?->code ?? false)
            <div class="flex justify-between items-center py-2">
                <span class="text-gray-600 dark:text-gray-300">Стол:</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ $booking->resource->code }}</span>
            </div>
        @endif
        
        @if($booking?->slots?->isNotEmpty() ?? false)
            <div class="flex justify-between items-center py-2">
                <span class="text-gray-600 dark:text-gray-300">Дата:</span>
                <span class="font-semibold text-gray-900 dark:text-white">
                    {{ $booking->slots->first()->slot_date ?? 'N/A' }}
                </span>
            </div>
            
            <div class="flex justify-between items-center py-2">
                <span class="text-gray-600 dark:text-gray-300">Время:</span>
                <span class="font-semibold text-gray-900 dark:text-white">
                    @foreach($booking->slots as $slot)
                        {{ $slot->slot_time }}@if(!$loop->last), @endif
                    @endforeach
                </span>
            </div>
        @endif
        
        <div class="flex justify-between items-center pt-4 border-t border-gray-200 dark:border-gray-600">
            <span class="text-lg font-bold text-gray-900 dark:text-white">Итого:</span>
            <span class="text-2xl font-bold text-green-600 dark:text-green-400">
                {{ $booking?->getTotalAmountFormatted() ?? '0 ₽' }}
            </span>
        </div>
        
        @if($isPaid)
            <div class="flex justify-between items-center pt-2">
                <span class="text-gray-600 dark:text-gray-300">Статус:</span>
                <span class="inline-flex items-center px-3 py-1 bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-200 rounded-full text-sm font-medium">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    Оплачено {{ $booking?->payment_method ? '(' . $booking->payment_method . ')' : '' }}
                </span>
            </div>
        @endif
    </div>
</div>