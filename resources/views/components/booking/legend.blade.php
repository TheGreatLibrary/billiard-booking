@props([
    'zones' => [],
])

<div class="flex flex-wrap items-center gap-6 mb-8 px-1">
    {{-- Статусы столов --}}
    <div class="flex items-center gap-2">
        <div class="w-4 h-3 rounded-sm bg-emerald-500 shadow-sm"></div>
        <span class="text-xs text-gray-600 dark:text-gray-400">Свободен</span>
    </div>
    <div class="flex items-center gap-2">
        <div class="w-4 h-3 rounded-sm bg-blue-500 shadow-sm"></div>
        <span class="text-xs text-gray-600 dark:text-gray-400">Выбран</span>
    </div>
    <div class="flex items-center gap-2">
        <div class="w-4 h-3 rounded-sm bg-gray-300 dark:bg-gray-600 shadow-sm"></div>
        <span class="text-xs text-gray-600 dark:text-gray-400">Занят</span>
    </div>

    {{-- Разделитель --}}
    @if(count($zones) > 0)
        <div class="w-px h-4 bg-gray-300 dark:bg-gray-600"></div>

        {{-- Зоны --}}
        @foreach($zones as $zone)
            @php $zoneData = is_array($zone) ? $zone : $zone->toArray(); @endphp
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full border-2" 
                     style="background-color: {{ $zoneData['color'] ?? '#3B82F6' }}30; border-color: {{ $zoneData['color'] ?? '#3B82F6' }};"></div>
                <span class="text-xs text-gray-600 dark:text-gray-400">
                    {{ $zoneData['name'] ?? 'Зона' }} <span class="text-gray-400 dark:text-gray-500">×{{ $zoneData['price_coef'] ?? '1.0' }}</span>
                </span>
            </div>
        @endforeach
    @endif
</div>