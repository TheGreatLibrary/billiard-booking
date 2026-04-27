@props([
    'booking' => null,
    'totalAmount' => 0,
    'wirePayBooking' => 'payBooking',
    'wireSkipPayment' => 'skipPayment',
    'wireGoBack' => 'goBack',
])

@php
    $amountFormatted = number_format($totalAmount, 0, '', ' ');
    $bookingData = $booking?->toArray() ?? [];
@endphp

<div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl dark:shadow-gray-900/40 p-6 sm:p-8 border border-gray-200 dark:border-gray-700">

    <x-booking.step-header title="Оплата бронирования" subtitle="Выберите способ оплаты" step="6" :wireGoBack="$wireGoBack" />

    @if(isset($bookingData['expires_at']))
        <x-booking.timer :expiresAt="$bookingData['expires_at']" class="mb-8" />
    @endif

    <x-booking.booking-details :booking="$booking" :totalAmount="$totalAmount" class="mb-8" />

    <div class="space-y-4 mb-8">
        <h3 class="font-semibold text-xl text-gray-900 dark:text-white mb-4">Выберите способ оплаты:</h3>

        <button data-pg="card" data-pg-amt="{{ $totalAmount }}" data-pg-af="{{ $amountFormatted }}" type="button"
                class="w-full p-5 border-2 border-blue-400 dark:border-blue-500 rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all text-left bg-white dark:bg-gray-800">
            <div class="flex items-center gap-4">
                <div class="w-11 h-11 bg-blue-100 dark:bg-blue-900/40 rounded-lg flex items-center justify-center text-xl">💳</div>
                <div class="flex-1">
                    <p class="font-semibold text-gray-900 dark:text-white">Банковская карта</p>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Visa, Mastercard, Мир</p>
                </div>
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </div>
        </button>

        <button data-pg="sbp" data-pg-amt="{{ $totalAmount }}" data-pg-af="{{ $amountFormatted }}" type="button"
                class="w-full p-5 border-2 border-green-400 dark:border-green-500 rounded-xl hover:bg-green-50 dark:hover:bg-green-900/20 transition-all text-left bg-white dark:bg-gray-800">
            <div class="flex items-center gap-4">
                <div class="w-11 h-11 bg-green-100 dark:bg-green-900/40 rounded-lg flex items-center justify-center text-xl">📱</div>
                <div class="flex-1">
                    <p class="font-semibold text-gray-900 dark:text-white">Система быстрых платежей</p>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Оплата по QR-коду</p>
                </div>
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </div>
        </button>
    </div>

    <div class="text-center">
        <button wire:click="{{ $wireSkipPayment }}" type="button" class="text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white text-sm hover:underline transition">Оплатить позже</button>
    </div>

    {{-- МОДАЛКА КАРТЫ --}}
    <div id="pg-card-overlay" style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;padding:16px;background:rgba(0,0,0,.5)">
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden border border-gray-200 dark:border-gray-700">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">🔒</div>
                    <div>
                        <p class="text-white font-semibold text-sm">Безопасная оплата</p>
                        <p class="text-blue-200 text-xs">Billiard Booking</p>
                    </div>
                </div>
                <button data-pg="close" class="text-white/70 hover:text-white transition text-lg" type="button">✕</button>
            </div>
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">К оплате</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $amountFormatted }} ₽</p>
            </div>
            <div id="pg-card-form" class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Номер карты</label>
                    <div class="relative">
                        <input id="pg-num" type="text" maxlength="19" placeholder="0000 0000 0000 0000"
                               class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg pl-4 pr-14 py-3 text-lg tracking-widest font-mono focus:ring-blue-500 focus:border-blue-500">
                        <span id="pg-brand" class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-500 dark:text-gray-400"></span>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Срок</label>
                        <input id="pg-exp" type="text" maxlength="5" placeholder="MM/YY"
                               class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg py-3 text-center text-lg font-mono tracking-wider focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">CVV</label>
                        <input id="pg-cvv" type="password" maxlength="3" placeholder="•••"
                               class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg py-3 text-center text-lg font-mono tracking-wider focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Имя держателя</label>
                    <input id="pg-holder" type="text" placeholder="IVAN IVANOV" style="text-transform:uppercase"
                           class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-4 py-3 tracking-wide focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div id="pg-error" style="display:none" class="text-red-600 dark:text-red-400 text-sm text-center p-3 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800"></div>
                <button data-pg="pay-card" type="button" class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition text-lg">
                    Оплатить {{ $amountFormatted }} ₽
                </button>
                <p class="text-center text-gray-400 dark:text-gray-500 text-[10px]">🔒 Данные защищены шифрованием</p>
                <p class="text-center text-gray-300 dark:text-gray-600 text-[10px]">Тест: 4242 4242 4242 4242 — успех · 4000 0000 0000 0002 — отказ</p>
            </div>
            <div id="pg-card-processing" style="display:none" class="p-12 text-center">
                <div class="w-14 h-14 mx-auto mb-4 border-4 border-blue-200 dark:border-blue-800 border-t-blue-600 dark:border-t-blue-400 rounded-full animate-spin"></div>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Обработка платежа...</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Не закрывайте окно</p>
                <div class="mt-4 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 overflow-hidden">
                    <div id="pg-card-bar" class="bg-blue-600 h-full rounded-full transition-all duration-500" style="width:0%"></div>
                </div>
            </div>
            <div id="pg-card-error" style="display:none" class="p-8 text-center">
                <div class="w-14 h-14 mx-auto mb-4 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center text-2xl">❌</div>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Платёж отклонён</p>
                <p id="pg-card-error-msg" class="text-sm text-gray-500 dark:text-gray-400 mb-6"></p>
                <button data-pg="card-retry" type="button" class="px-6 py-2.5 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg transition text-sm">Попробовать снова</button>
            </div>
        </div>
    </div>

    {{-- МОДАЛКА СБП --}}
    <div id="pg-sbp-overlay" style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;padding:16px;background:rgba(0,0,0,.5)">
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden border border-gray-200 dark:border-gray-700">
            <div class="bg-gradient-to-r from-green-600 to-emerald-600 p-5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">📱</div>
                    <div>
                        <p class="text-white font-semibold text-sm">Оплата по СБП</p>
                        <p class="text-green-200 text-xs">Система быстрых платежей</p>
                    </div>
                </div>
                <button data-pg="close" type="button" class="text-white/70 hover:text-white transition text-lg">✕</button>
            </div>
            <div id="pg-sbp-form" class="p-6 text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Сумма к оплате</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mb-6">{{ $amountFormatted }} ₽</p>
                <div class="bg-white p-4 rounded-xl inline-block border border-gray-200 mb-4">
                    <canvas id="pg-qr" width="180" height="180" class="block"></canvas>
                </div>
                <p class="text-sm text-gray-700 dark:text-gray-300 mb-1">Отсканируйте QR-код</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mb-6">в мобильном приложении вашего банка</p>
                <button data-pg="pay-sbp" type="button" class="w-full py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition text-sm">Я оплатил</button>
            </div>
            <div id="pg-sbp-processing" style="display:none" class="p-12 text-center">
                <div class="w-14 h-14 mx-auto mb-4 border-4 border-green-200 dark:border-green-800 border-t-green-600 dark:border-t-green-400 rounded-full animate-spin"></div>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Проверяем оплату...</p>
                <div class="mt-4 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 overflow-hidden">
                    <div id="pg-sbp-bar" class="bg-green-600 h-full rounded-full transition-all duration-500" style="width:0%"></div>
                </div>
            </div>
        </div>
    </div>
</div>