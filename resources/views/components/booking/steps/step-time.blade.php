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
    'multiTable' => false,
])

<div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl dark:shadow-gray-900/40 p-6 sm:p-8 border border-gray-200 dark:border-gray-700">
    <!-- Заголовок с кнопкой назад -->
    <x-booking.step-header 
        title="Выберите время"
        subtitle="{{ $multiTable ? 'Выберите дату и время — потом подберёте столы' : 'Выберите дату и время для бронирования' }}"
        step="2"
        :wireGoBack="$wireGoBack"
    />

    <!-- Информационная панель -->
    <div class="grid grid-cols-1 {{ $multiTable ? '' : 'md:grid-cols-2' }} gap-6 mb-8">
        <x-booking.info-item 
            icon="location-marker"
            label="Заведение"
            :value="$placeData['place']['name'] ?? ''"
            color="blue"
        />
        
        @if(!$multiTable && $resource_id)
            <x-booking.info-item 
                icon="table"
                label="Стол"
                :value="collect($placeData['resources'] ?? [])->firstWhere('id', $resource_id)['code'] ?? 'N/A'"
                color="green"
            />
        @endif
    </div>

    @if($multiTable)
        <div class="mb-6 p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl border border-indigo-200 dark:border-indigo-800">
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-sm text-indigo-800 dark:text-indigo-200">
                    Цены указаны <strong>«от»</strong> — минимальная стоимость среди доступных столов.
                    Точные цены увидите при выборе столов на следующем шаге.
                </p>
            </div>
        </div>
    @endif

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
        :showPricePrefix="$multiTable"
    />

    <!-- Быстрый выбор -->
    <x-booking.quick-select 
        :wireQuickSelect="$wireQuickSelect"
        :wireClearSlots="$wireClearSlots"
    />

    <!-- Итого и кнопка -->
    <x-booking.summary-action 
        :totalAmount="$multiTable ? 0 : $totalAmount"
        :selectedCount="count($selectedSlots)"
        :wireProceed="$wireProceedToEquipment"
        :disabled="count($selectedSlots) === 0"
        :buttonText="$multiTable ? 'Далее: Выбрать столы' : 'Далее: Доп. услуги'"
        :showCount="$multiTable"
    />
</div>