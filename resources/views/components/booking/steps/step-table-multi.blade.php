@props([
    'placeData' => [],
    'selectedResources' => [],
    'availableResourceIds' => [],
    'resourcePrices' => [],
    'selectedSlots' => [],
    'date' => null,
    'totalAmount' => 0,
])

@php
    $zones = $placeData['zones'] ?? [];
    $resources = $placeData['resources'] ?? [];
    $selectedCount = count($selectedResources);
@endphp

<div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl dark:shadow-gray-900/40 p-6 sm:p-8 border border-gray-200 dark:border-gray-700">
    <x-booking.step-header 
        title="Выберите столы"
        subtitle="Выберите один или несколько столов для бронирования"
        step="3"
        wireGoBack="goBack"
    />

    {{-- Инфо о времени --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="p-4 bg-blue-50 dark:bg-blue-900/30 rounded-xl border border-blue-200 dark:border-blue-800">
            <p class="text-sm text-gray-600 dark:text-gray-400">Заведение</p>
            <p class="font-bold text-gray-900 dark:text-white">{{ $placeData['place']['name'] ?? '' }}</p>
        </div>
        <div class="p-4 bg-teal-50 dark:bg-teal-900/30 rounded-xl border border-teal-200 dark:border-teal-800">
            <p class="text-sm text-gray-600 dark:text-gray-400">Дата</p>
            <p class="font-bold text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}</p>
        </div>
        <div class="p-4 bg-amber-50 dark:bg-amber-900/30 rounded-xl border border-amber-200 dark:border-amber-800">
            <p class="text-sm text-gray-600 dark:text-gray-400">Время</p>
            <p class="font-bold text-gray-900 dark:text-white">{{ implode(', ', $selectedSlots) }} ({{ count($selectedSlots) }} ч.)</p>
        </div>
    </div>

    {{-- Подсказка --}}
    <div class="mb-6 p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl border border-indigo-200 dark:border-indigo-800">
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-sm text-indigo-800 dark:text-indigo-200">
                <strong>Зелёные</strong> столы доступны. Вы можете выбрать <strong>несколько</strong>.
                Серые столы заняты в выбранное время.
            </p>
        </div>
    </div>

    {{-- Карта зала (новый компонент) --}}
    <x-booking.table-map 
        :placeData="$placeData"
        wireSelectResource="toggleResource"
        :multiSelect="true"
        :selectedResources="$selectedResources"
        :availableResourceIds="$availableResourceIds"
        :resourcePrices="$resourcePrices"
    />

    {{-- Легенда --}}
    <x-booking.legend :zones="$zones" />

    {{-- Выбранные столы --}}
    @if($selectedCount > 0)
        <div class="mb-6 p-5 bg-blue-50 dark:bg-blue-900/20 rounded-xl border-2 border-blue-200 dark:border-blue-800">
            <h4 class="font-semibold text-blue-900 dark:text-blue-100 mb-3 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                Выбрано столов: {{ $selectedCount }}
            </h4>
            <div class="flex flex-wrap gap-2">
                @foreach($selectedResources as $rid)
                    @php
                        $res = collect($resources)->firstWhere('id', $rid);
                        $price = $resourcePrices[$rid] ?? 0;
                    @endphp
                    @if($res)
                        <span class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500 text-white rounded-full text-sm font-medium shadow-sm">
                            {{ $res['code'] }} — {{ number_format($price, 0) }} ₽
                            <button wire:click="toggleResource({{ $rid }})" class="hover:bg-blue-600 rounded-full p-0.5 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </span>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    <x-booking.summary-action 
        :totalAmount="$totalAmount"
        :selectedCount="$selectedCount"
        wireProceed="proceedToEquipment"
        :disabled="$selectedCount === 0"
        buttonText="Далее: Доп. услуги"
        label="столов"
    />
</div>