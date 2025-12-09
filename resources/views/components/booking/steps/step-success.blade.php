@props([
    'booking' => null,
    'totalAmount' => 0,
])

@php
    $bookingData = $booking?->toArray() ?? [];
    $isPaid = $booking?->isPaid() ?? false;
@endphp

<div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl dark:shadow-gray-900/40 p-8 sm:p-12 border border-gray-200 dark:border-gray-700 text-center">
    <!-- Анимация успеха -->
    <div class="mb-8">
        <div class="w-32 h-32 mx-auto mb-6">
            <div class="w-full h-full bg-gradient-to-br from-green-100 to-emerald-100 dark:from-green-900/40 dark:to-emerald-900/40 rounded-full flex items-center justify-center">
                <svg class="w-16 h-16 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
            </div>
        </div>
        
        <!-- Заголовок -->
        <h2 class="text-4xl font-bold text-green-600 dark:text-green-400 mb-4">
            @if($isPaid)
                Оплата прошла успешно!
            @else
                Бронирование создано!
            @endif
        </h2>
        
        <p class="text-xl text-gray-600 dark:text-gray-300 mb-2">
            @if($isPaid)
                Ваше бронирование подтверждено и оплачено
            @else
                Бронирование ожидает оплаты
            @endif
        </p>
        
        <p class="text-gray-500 dark:text-gray-400">
            Номер бронирования: 
            <strong class="text-gray-900 dark:text-white">#{{ $bookingData['id'] ?? '' }}</strong>
        </p>
    </div>

    <!-- Статус оплаты -->
    @if(!$isPaid)
        <x-booking.payment-warning class="mb-8" />
    @endif

    <!-- Детали бронирования -->
    <x-booking.success-summary 
        :booking="$booking"
        :isPaid="$isPaid"
        class="mb-8 text-left"
    />

    <!-- Действия -->
    <div class="space-y-4">
        <!-- На главную -->
        <x-auth.button
            href="/" 
            variant="primary"
            size="xl"
            class="w-full"
        >
            На главную
            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
        </x-auth.button>
        
        <!-- Оплатить сейчас (если не оплачено) -->
        @if(!$isPaid)
            <x-auth.button 
                wire:click="$set('step', 6)"
                variant="success"
                size="xl"
                class="w-full"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Оплатить сейчас
            </x-auth.button>
        @endif
        
        <!-- Кнопка "Мои бронирования" -->
        <x-auth.button 
            href="{{ route('dashboard') ?? '/' }}"
            variant="ghost"
            size="xl"
            class="w-full"
        >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Мои бронирования
        </x-auth.button>
    </div>
</div>