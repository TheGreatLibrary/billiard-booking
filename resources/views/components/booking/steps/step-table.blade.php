@props([
    'placeData' => [],
    'resource_id' => null,
    'wireSelectResource' => 'selectResource',
    'wireProceedToTimeSelection' => 'proceedToTimeSelection',
    'wireGoBack' => 'goBack',
])

<div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl dark:shadow-gray-900/40 p-6 sm:p-8 border border-gray-200 dark:border-gray-700">
    <!-- Заголовок с кнопкой назад -->
    <x-booking.step-header 
        title="Выберите стол"
        subtitle="Выберите подходящий стол в выбранном заведении"
        step="2"
        :wireGoBack="$wireGoBack"
    />

    <!-- Информационная панель -->
    <x-booking.info-panel 
        :placeData="$placeData"
        :resourceId="$resource_id"
    />

    <!-- Карта зала -->
    <x-booking.table-map 
        :placeData="$placeData"
        :resourceId="$resource_id"
        :wireSelectResource="$wireSelectResource"
    />

    <!-- Легенда -->
    <x-booking.legend 
        :zones="$placeData['zones'] ?? []"
    />

    <!-- Кнопка продолжить -->
    <div class="flex justify-end mt-8">
        <x-auth.button 
            wire:click="{{ $wireProceedToTimeSelection }}"
            variant="primary"
            size="lg"
            :disabled="!$resource_id"
            class="gap-3"
        >
            <span>Далее: Выбрать время</span>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </x-auth.button>
    </div>
</div>