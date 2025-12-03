@props([
    'placeData' => [],
    'resource_id' => null,
    'date' => null,
    'selectedSlots' => [],
    'availableSlots' => [],
    'totalAmount' => 0,
    'wireToggleSlot' => 'toggleSlot',
    'wireQuickSelect' => 'quickSelect',
    'wireClearSlots' => 'clearSlots',
    'wireProceedToEquipment' => 'proceedToEquipment',
    'wireGoBack' => 'goBack',
])

<div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl dark:shadow-gray-900/40 p-6 sm:p-8 border border-gray-200 dark:border-gray-700">
    <!-- Заголовок с кнопкой назад -->
    <x-booking.step-header 
        title="Выберите время"
        subtitle="Выберите дату и время для бронирования"
        step="3"
        :wireGoBack="$wireGoBack"
    />

    <!-- Информационная панель -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <x-booking.info-item 
            icon="location-marker"
            label="Заведение"
            :value="$placeData['place']['name'] ?? ''"
            color="blue"
        />
        
        <x-booking.info-item 
            icon="table"
            label="Стол"
            :value="collect($placeData['resources'] ?? [])->firstWhere('id', $resource_id)['code'] ?? 'N/A'"
            color="green"
        />
    </div>

    <!-- Выбор даты -->
    <div class="mb-8">
        <x-auth.input 
            wire:model.live="date"
            type="date"
            label="Дата бронирования"
            icon="calendar"
            required
            :min="now()->format('Y-m-d')"
            class="text-lg py-4"
        />
    </div>

    <!-- Выбор времени -->
    <x-booking.time-slots 
        :availableSlots="$availableSlots"
        :selectedSlots="$selectedSlots"
        :wireToggleSlot="$wireToggleSlot"
    />

    <!-- Быстрый выбор -->
    <x-booking.quick-select 
        :wireQuickSelect="$wireQuickSelect"
        :wireClearSlots="$wireClearSlots"
    />

    <!-- Итого и кнопка -->
    <x-booking.summary-action 
        :totalAmount="$totalAmount"
        :selectedCount="count($selectedSlots)"
        :wireProceed="$wireProceedToEquipment"
        :disabled="count($selectedSlots) === 0"
        buttonText="Далее: Доп. услуги"
    />
</div>