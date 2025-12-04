@props([
    'placeData' => [],
    'resource_id' => null,
    'date' => null,
    'selectedSlots' => [],
    'equipment' => [],
    'totalAmount' => 0,
    'comment' => '',
    'wireCreatePendingBooking' => 'createPendingBooking',
    'wireGoBack' => 'goBack',
])

@php
    $selectedResource = collect($placeData['resources'] ?? [])->firstWhere('id', $resource_id);
@endphp

<div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl dark:shadow-gray-900/40 p-6 sm:p-8 border border-gray-200 dark:border-gray-700">
    <!-- Заголовок -->
    <x-booking.step-header 
        title="Ваши данные"
        subtitle="Заполните информацию для бронирования"
        step="5"
        :wireGoBack="$wireGoBack"
    />

    <!-- Информация о пользователе -->
    @guest
        <!-- Форма для гостей -->
        <x-booking.guest-form class="mb-8" />
    @else
        <!-- Информация для авторизованных -->
        <x-booking.user-info class="mb-8" />
    @endguest

    <!-- Комментарий -->
    <div class="mb-8">
        <x-auth.input 
            wire:model="comment"
            type="textarea"
            label="Комментарий к бронированию"
            icon="chat"
            placeholder="Дополнительные пожелания или комментарии..."
            rows="4"
        />
    </div>

    <!-- Сводка бронирования -->
    <x-booking.summary-card 
        :placeData="$placeData"
        :resource="$selectedResource"
        :date="$date"
        :selectedSlots="$selectedSlots"
        :equipment="$equipment"
        :totalAmount="$totalAmount"
        class="mb-8"
    />

    <!-- Кнопка оплаты -->
    <x-auth.button 
        wire:click="{{ $wireCreatePendingBooking }}"
        wire:loading.attr="disabled"
        variant="primary"
        size="xl"
        class="w-full gap-3"
    >
        <span wire:loading.remove>Перейти к оплате</span>
        <span wire:loading>Создание бронирования...</span>
        <x-booking.loading-spinner wire:loading />
        <svg wire:loading.remove class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
    </x-auth.button>
</div>