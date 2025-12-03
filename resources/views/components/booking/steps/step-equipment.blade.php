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
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach($availableEquipment as $eq)
                <x-booking.equipment-card 
                    :equipment="$eq"
                    :wireAddEquipment="$wireAddEquipment"
                />
            @endforeach
        </div>
    @else
        <x-shared.empty-state 
            icon="cube"
            title="Оборудование недоступно"
            description="Дополнительное оборудование временно отсутствует"
            class="mb-8"
        />
    @endif

    <!-- Выбранное оборудование -->
    @if(count($equipment) > 0)
        <x-booking.selected-equipment 
            :equipment="$equipment"
            :wireUpdateEquipmentQty="$wireUpdateEquipmentQty"
            :wireRemoveEquipment="$wireRemoveEquipment"
            class="mb-8"
        />
    @endif

    <!-- Действия -->
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-8 border-t border-gray-200 dark:border-gray-700">
        <x-auth.button 
            wire:click="{{ $wireSkipEquipment }}"
            variant="secondary"
            size="lg"
            class="w-full sm:w-auto"
        >
            Пропустить
        </x-auth.button>
        
        <div class="flex flex-col sm:flex-row items-center gap-4 w-full sm:w-auto">
            <x-booking.total-amount 
                :amount="$totalAmount"
                size="lg"
            />
            
            <x-auth.button 
                wire:click="{{ $wireProceedToClientData }}"
                variant="primary"
                size="lg"
                class="w-full sm:w-auto"
            >
                Далее: Данные клиента
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </x-auth.button>
        </div>
    </div>
</div>