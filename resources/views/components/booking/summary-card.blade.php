@props([
    'placeData' => [],
    'resource' => [],
    'date' => null,
    'selectedSlots' => [],
    'equipment' => [],
    'totalAmount' => 0,
])

@php
    $resourceData = is_array($resource) ? $resource : $resource?->toArray();
    $placeDataArray = is_array($placeData) ? $placeData : $placeData?->toArray();
@endphp

<div {{ $attributes->merge(['class' => 'bg-gray-50 dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700']) }}>
    <h3 class="font-semibold text-xl text-gray-900 dark:text-white mb-6 flex items-center gap-2">
        <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
        </svg>
        Сводка бронирования
    </h3>
    
    <div class="space-y-4">
        <!-- Место -->
        <x-booking.summary-item 
            label="Заведение:"
            :value="$placeDataArray['place']['name'] ?? ''"
            icon="location-marker"
        />
        
        <!-- Стол -->
        <x-booking.summary-item 
            label="Стол:"
            :value="$resourceData['code'] ?? 'N/A'"
            icon="table"
        />
        
        <!-- Дата -->
        @if($date)
            <x-booking.summary-item 
                label="Дата:"
                :value="\Carbon\Carbon::parse($date)->translatedFormat('d F Y')"
                icon="calendar"
            />
        @endif
        
        <!-- Время -->
        @if(count($selectedSlots) > 0)
            <div class="py-3 border-b border-gray-200 dark:border-gray-600">
                <div class="flex items-start gap-2 mb-2">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-gray-600 dark:text-gray-300 font-medium">Время:</span>
                </div>
                <div class="flex flex-wrap gap-2 ml-7">
                    @foreach($selectedSlots as $time)
                        <span class="inline-flex items-center px-3 py-1 bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-200 rounded-full text-sm font-medium">
                            {{ $time }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif
        
        <!-- Оборудование -->
        @if(count($equipment) > 0)
            <div class="py-3 border-b border-gray-200 dark:border-gray-600">
                <div class="flex items-center gap-2 mb-3">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <span class="text-gray-600 dark:text-gray-300 font-medium">Оборудование:</span>
                </div>
                <div class="space-y-2 ml-7">
                    @foreach($equipment as $item)
                        @php
                            $itemData = is_array($item) ? $item : $item->toArray();
                        @endphp
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <span class="text-gray-900 dark:text-white">{{ $itemData['name'] ?? '' }}</span>
                                <span class="text-sm text-gray-500 dark:text-gray-400">×{{ $itemData['qty'] ?? 1 }}</span>
                            </div>
                            <span class="font-semibold text-green-600 dark:text-green-400">
                                {{ number_format((($itemData['price'] ?? 0) * ($itemData['qty'] ?? 1)), 0) }} ₽
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        
        <!-- Итого -->
        <div class="flex justify-between items-center pt-4">
            <span class="text-xl font-bold text-gray-900 dark:text-white">ИТОГО К ОПЛАТЕ:</span>
            <span class="text-3xl font-bold text-green-600 dark:text-green-400">
                {{ number_format($totalAmount, 0) }} ₽
            </span>
        </div>
    </div>
</div>