@props([
    'booking' => null,
    'totalAmount' => 0,
])

@php
    $bookingData = $booking?->toArray() ?? [];
    $isPaid = $booking?->isPaid() ?? false;
@endphp

<div {{ $attributes->merge(['class' => 'bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6']) }}>
    <h3 class="font-semibold text-xl text-gray-900 dark:text-white mb-4 flex items-center gap-2">
        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
        </svg>
        Детали бронирования
    </h3>
    
    <div class="space-y-3 text-sm">
        <!-- Номер бронирования -->
        <div class="flex justify-between">
            <span class="text-gray-600 dark:text-gray-300">Номер:</span>
            <span class="font-semibold text-gray-900 dark:text-white">#{{ $bookingData['id'] ?? 'N/A' }}</span>
        </div>
        
        <!-- Статус -->
        <div class="flex justify-between">
            <span class="text-gray-600 dark:text-gray-300">Статус:</span>
            @if($isPaid)
                <span class="inline-flex items-center px-3 py-1 bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-200 rounded-full text-sm font-medium">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    Оплачено
                </span>
            @else
                <span class="inline-flex items-center px-3 py-1 bg-yellow-100 dark:bg-yellow-900/40 text-yellow-800 dark:text-yellow-200 rounded-full text-sm font-medium">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                    </svg>
                    Ожидает оплаты
                </span>
            @endif
        </div>
        
        <!-- Заведение -->
        @if($booking?->place?->name ?? false)
            <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-300">Заведение:</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ $booking->place->name }}</span>
            </div>
        @endif
        
        <!-- Стол -->
        @if($booking?->resource?->code ?? false)
            <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-300">Стол:</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ $booking->resource->code }}</span>
            </div>
        @endif
        
        <!-- Дата и время -->
        @if($booking?->slots?->isNotEmpty() ?? false)
            <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-300">Дата:</span>
                <span class="font-semibold text-gray-900 dark:text-white">
                    {{ $booking->slots->first()->slot_date ?? 'N/A' }}
                </span>
            </div>
            
            <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-300">Время:</span>
                <span class="font-semibold text-gray-900 dark:text-white">
                    @foreach($booking->slots as $slot)
                        {{ $slot->slot_time }}@if(!$loop->last), @endif
                    @endforeach
                </span>
            </div>
        @endif
        
        <!-- Сумма к оплате -->
        <div class="flex justify-between items-center pt-3 border-t border-gray-200 dark:border-gray-600">
            <span class="text-lg font-semibold text-gray-900 dark:text-white">Сумма к оплате:</span>
            <span class="text-2xl font-bold text-green-600 dark:text-green-400">
                {{ number_format($totalAmount, 2) }} ₽
            </span>
        </div>
        
        <!-- Способ оплаты (если оплачено) -->
        @if($isPaid && $booking?->payment_method ?? false)
            <div class="flex justify-between pt-2">
                <span class="text-gray-600 dark:text-gray-300">Способ оплаты:</span>
                <span class="font-semibold text-gray-900 dark:text-white capitalize">
                    {{ $booking->payment_method }}
                </span>
            </div>
        @endif
    </div>
</div>