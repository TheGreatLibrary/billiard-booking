@props([
    'booking' => null,
    'totalAmount' => 0,
])

@php
    $bookingData = $booking?->toArray() ?? [];
    $isPaid = $booking?->isPaid() ?? false;
    $bookedResources = $booking ? $booking->getBookedResources() : collect();
    $uniqueSlotTimes = $booking?->slots?->pluck('slot_time')->unique()->sort()->values() ?? collect();
    $slotDate = $booking?->slots?->first()?->slot_date ?? 'N/A';
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
    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 mb-8 text-left">
        <h3 class="font-bold text-xl text-gray-900 dark:text-white mb-6 flex items-center gap-2">
            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            Детали бронирования
        </h3>
        
        <div class="space-y-4">
            @if($booking?->place?->name)
                <div class="flex justify-between items-center py-2">
                    <span class="text-gray-600 dark:text-gray-300">Заведение:</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $booking->place->name }}</span>
                </div>
            @endif
            
            <!-- Столы (мульти) -->
            <div class="py-2">
                <span class="text-gray-600 dark:text-gray-300">Столы ({{ $bookedResources->count() }}):</span>
                <div class="flex flex-wrap gap-2 mt-2">
                    @foreach($bookedResources as $res)
                        <span class="inline-flex items-center px-3 py-1 bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-200 rounded-full text-sm font-medium">
                            {{ $res->code }}
                            @if($res->productModel)
                                <span class="ml-1 opacity-75">· {{ $res->productModel->name }}</span>
                            @endif
                        </span>
                    @endforeach
                </div>
            </div>
            
            <div class="flex justify-between items-center py-2">
                <span class="text-gray-600 dark:text-gray-300">Дата:</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ $slotDate }}</span>
            </div>
            
            <div class="flex justify-between items-center py-2">
                <span class="text-gray-600 dark:text-gray-300">Время:</span>
                <span class="font-semibold text-gray-900 dark:text-white">
                    {{ $uniqueSlotTimes->implode(', ') }}
                </span>
            </div>
            
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

    <!-- Действия -->
    <div class="space-y-4">
        <x-auth.button href="/" variant="primary" size="xl" class="w-full">
            На главную
            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
        </x-auth.button>
        
        @if(!$isPaid)
            <x-auth.button wire:click="$set('step', 6)" variant="success" size="xl" class="w-full">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Оплатить сейчас
            </x-auth.button>
        @endif
        
        <x-auth.button href="{{ route('dashboard') ?? '/' }}" variant="ghost" size="xl" class="w-full">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Мои бронирования
        </x-auth.button>
    </div>
</div>
