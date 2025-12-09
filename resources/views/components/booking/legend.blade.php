@props([
    'zones' => [],
])

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    {{-- Легенда зон --}}
    @if(count($zones) > 0)
        <div class="p-6 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                </svg>
                Зоны заведения
            </h3>
            <div class="space-y-3">
                @foreach($zones as $zone)
                    @php
                        $zoneData = is_array($zone) ? $zone : $zone->toArray();
                    @endphp
                    <div class="flex items-center justify-between p-3 rounded-lg border-2 transition-colors hover:bg-white dark:hover:bg-gray-700"
                         style="border-color: {{ $zoneData['color'] ?? '#3B82F6' }}; background-color: {{ $zoneData['color'] ?? '#3B82F6' }}10;">
                        <div class="flex items-center gap-3">
                            <div class="w-4 h-4 rounded-full" style="background-color: {{ $zoneData['color'] ?? '#3B82F6' }};"></div>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $zoneData['name'] ?? 'Зона' }}</span>
                        </div>
                        <span class="text-sm font-semibold px-2 py-1 rounded-full bg-white dark:bg-gray-600 text-gray-700 dark:text-gray-300">
                            ×{{ $zoneData['price_coef'] ?? 1.0 }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Легенда статусов --}}
    <div class="p-6 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Статусы столов
        </h3>
        <div class="space-y-4">
            <div class="flex items-center gap-4 p-3 rounded-lg bg-white dark:bg-gray-700 border border-green-200 dark:border-green-800">
                <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 border-2 border-green-500 dark:border-green-400 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <span class="font-medium text-gray-900 dark:text-white">Доступен для бронирования</span>
            </div>
            <div class="flex items-center gap-4 p-3 rounded-lg bg-white dark:bg-gray-700 border border-blue-200 dark:border-blue-800">
                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 border-2 border-blue-500 dark:border-blue-400 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <span class="font-medium text-gray-900 dark:text-white">Выбранный стол</span>
            </div>
            <div class="flex items-center gap-4 p-3 rounded-lg bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 opacity-60">
                <div class="w-8 h-8 bg-gray-200 dark:bg-gray-600 border-2 border-gray-400 dark:border-gray-500 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <span class="font-medium text-gray-900 dark:text-white">Занят / Недоступен</span>
            </div>
        </div>
    </div>
</div>