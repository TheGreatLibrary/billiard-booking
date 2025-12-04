@props([
    'booking' => null,
    'totalAmount' => 0,
    'wirePayBooking' => 'payBooking',
    'wireSkipPayment' => 'skipPayment',
    'wireGoBack' => 'goBack',
])

@php
    $bookingData = $booking?->toArray() ?? [];
@endphp

<div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl dark:shadow-gray-900/40 p-6 sm:p-8 border border-gray-200 dark:border-gray-700">
    <!-- Заголовок -->
    <x-booking.step-header 
        title="Оплата бронирования"
        subtitle="Выберите способ оплаты для подтверждения бронирования"
        step="6"
        :wireGoBack="$wireGoBack"
    />

    <!-- Таймер -->
    @if(isset($bookingData['expires_at']))
        <x-booking.timer 
            :expiresAt="$bookingData['expires_at']"
            class="mb-8"
        />
    @endif

    <!-- Детали бронирования -->
    <x-booking.booking-details 
        :booking="$booking"
        :totalAmount="$totalAmount"
        class="mb-8"
    />

    <!-- Способы оплаты -->
    <div class="space-y-4 mb-8">
        <h3 class="font-semibold text-xl text-gray-900 dark:text-white mb-4">Выберите способ оплаты:</h3>
        
        <!-- Карта -->
        <x-booking.payment-method 
            method="card"
            title="💳 Банковская карта"
            description="Visa, Mastercard, Мир"
            icon="credit-card"
            :wirePayBooking="$wirePayBooking"
            color="blue"
        />
        
        <!-- Онлайн перевод -->
        <x-booking.payment-method 
            method="online"
            title="🌐 Онлайн перевод (СБП)"
            description="Быстрый перевод по номеру телефона"
            icon="globe"
            :wirePayBooking="$wirePayBooking"
            color="green"
        />
        
        <!-- Наличные -->
        <x-booking.payment-method 
            method="cash"
            title="💵 Наличными при посещении"
            description="Оплата в заведении"
            icon="cash"
            :wirePayBooking="$wirePayBooking"
            color="amber"
        />
    </div>

    <!-- Пропустить оплату -->
    <div class="text-center">
        <button 
            wire:click="{{ $wireSkipPayment }}"
            type="button"
            class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white font-medium transition-colors hover:underline"
        >
            Оплатить позже
        </button>
    </div>
</div>