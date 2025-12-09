@props([
    'availableEquipment' => [],
    'equipment' => [],
    'totalAmount' => 0,
    'wireAddEquipment' => 'addEquipment',
    'wireUpdateEquipmentQty' => 'updateEquipmentQty',
    'wireRemoveEquipment' => 'removeEquipment',
    'wireSkipEquipment' => 'skipEquipment',
    'wireProceedToClientData' => 'proceedToClientData',
    'wireGoBack' => 'goBack',
])

<div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl dark:shadow-gray-900/40 p-6 sm:p-8 border border-gray-200 dark:border-gray-700">
    <!-- Заголовок -->
    <x-booking.step-header 
        title="Дополнительное оборудование"
        subtitle="Выберите дополнительное оборудование для вашего бронирования"
        step="4"
        :wireGoBack="$wireGoBack"
    />

    <!-- Доступное оборудование -->
    @if(count($availableEquipment) > 0)
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Доступное оборудование ({{ count($availableEquipment) }})
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($availableEquipment as $eq)
                    <x-booking.equipment-card 
                        :equipment="$eq"
                        :wireAddEquipment="$wireAddEquipment"
                    />
                @endforeach
            </div>
        </div>
    @else
        <div class="mb-8">
            <x-shared.empty-state 
                icon="cube"
                title="Оборудование недоступно"
                description="Дополнительное оборудование временно отсутствует на выбранное время"
                class="mb-8"
            />
            <p class="text-sm text-gray-600 dark:text-gray-400 text-center">
                Попробуйте выбрать другое время или обратитесь к администратору
            </p>
        </div>
    @endif

    <!-- Выбранное оборудование -->
    <x-booking.selected-equipment 
        :equipment="$equipment"
        :wireUpdateEquipmentQty="$wireUpdateEquipmentQty"
        :wireRemoveEquipment="$wireRemoveEquipment"
        class="mb-8"
    />

    <!-- Итоговая сумма -->
    <div class="mb-8">
        <div class="flex justify-between items-center p-4 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-gray-800 dark:to-gray-900 rounded-xl border border-green-200 dark:border-green-900">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Итого за оборудование:</p>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                    {{ number_format($totalAmount, 0) }} ₽
                </p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-600 dark:text-gray-400">Выбрано позиций:</p>
                <p class="text-xl font-bold text-blue-600 dark:text-blue-400">
                    {{ count($equipment) }}
                </p>
            </div>
        </div>
    </div>

    <!-- Действия -->
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-8 border-t border-gray-200 dark:border-gray-700">
        <div class="flex gap-4 w-full sm:w-auto">
            <x-auth.button 
                wire:click="{{ $wireGoBack }}"
                variant="secondary"
                size="lg"
                class="gap-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Назад
            </x-auth.button>
            
            <x-auth.button 
                wire:click="{{ $wireSkipEquipment }}"
                variant="secondary"
                size="lg"
                class="gap-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Пропустить оборудование
            </x-auth.button>
        </div>
        
        <div class="flex flex-col sm:flex-row items-center gap-4 w-full sm:w-auto">
            <x-auth.button 
                wire:click="{{ $wireProceedToClientData }}"
                variant="primary"
                size="lg"
                class="w-full sm:w-auto gap-2"
            >
                Далее: Данные клиента
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </x-auth.button>
        </div>
    </div>
</div>