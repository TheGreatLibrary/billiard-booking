<div class="min-h-screen bg-gray-50 p-4">
    <div class="max-w-screen-2xl mx-auto">
        {{-- Заголовок --}}
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Редактор зон</h1>
            <p class="text-gray-600 mt-1">Создавайте и редактируйте зоны с различными ценовыми коэффициентами</p>
        </div>

        {{-- Алерты --}}
        @if (session()->has('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        {{-- Выбор места --}}
        <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Заведение</label>
            <select wire:model.live="placeId"
                    class="w-full max-w-md border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="">Выберите заведение</option>
                @foreach($places as $place)
                    <option value="{{ $place->id }}">{{ $place->name }}</option>
                @endforeach
            </select>
        </div>

        @if($place)
            <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
                {{-- Левая панель: Сетка --}}
                <div class="xl:col-span-3 bg-white rounded-lg shadow-sm p-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-xl font-semibold text-gray-900">
                            Карта зала ({{ $gridWidth }}×{{ $gridHeight }})
                        </h2>
                        
                        {{-- Индикатор режима --}}
                        <div class="flex items-center space-x-2">
                            @if($mode === 'draw')
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
                                    🎨 Рисование новой зоны
                                </span>
                            @elseif($mode === 'edit')
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-sm font-medium rounded-full">
                                    ✏️ Редактирование зоны
                                </span>
                            @else
                                <span class="px-3 py-1 bg-gray-100 text-gray-800 text-sm font-medium rounded-full">
                                    👆 Режим просмотра
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Сетка --}}
                    <div class="border-2 border-gray-300 rounded-lg overflow-auto bg-gray-50" 
                         style="max-height: calc(100vh - 300px);">
                        <div class="inline-block min-w-full p-4">
                            <div class="grid gap-0" 
                                 style="grid-template-columns: repeat({{ $gridWidth }}, 1fr); 
                                        grid-template-rows: repeat({{ $gridHeight }}, 1fr);">
                                @for($y = 0; $y < $gridHeight; $y++)
                                    @for($x = 0; $x < $gridWidth; $x++)
                                        @php
                                            // Проверяем, принадлежит ли ячейка какой-то зоне
                                            $cellZone = null;
                                            foreach($zones as $zone) {
                                                if(in_array(['x' => $x, 'y' => $y], $zone['coordinates'])) {
                                                    $cellZone = $zone;
                                                    break;
                                                }
                                            }
                                            
                                            // Проверяем, выбрана ли ячейка сейчас
                                            $isSelected = in_array(['x' => $x, 'y' => $y], $selectedCells);
                                            
                                            // Проверяем, принадлежит ли выделенной зоне
                                            $isInSelectedZone = false;
                                            if($selectedZoneId && $cellZone && $cellZone['id'] === $selectedZoneId) {
                                                $isInSelectedZone = true;
                                            }
                                        @endphp
                                        
                                        <div
                                            wire:click="toggleCell({{ $x }}, {{ $y }})"
                                            class="aspect-square border border-gray-200 cursor-pointer transition-all duration-150
                                                   hover:border-gray-400 relative group
                                                   @if($isSelected)
                                                       ring-2 ring-blue-500 ring-inset z-10
                                                   @elseif($isInSelectedZone)
                                                       ring-2 ring-opacity-50 z-10
                                                   @endif"
                                            style="
                                                background-color: {{ $isSelected ? $zoneColor : ($cellZone ? $cellZone['color'] : 'white') }};
                                                opacity: {{ $isSelected ? '0.9' : ($isInSelectedZone ? '0.8' : ($cellZone ? '0.5' : '1')) }};
                                                min-width: 40px;
                                                min-height: 40px;
                                            "
                                            title="Ячейка ({{ $x }}, {{ $y }}){{ $cellZone ? ' - ' . $cellZone['name'] : '' }}"
                                        >
                                            {{-- Координаты при наведении --}}
                                            <span class="absolute inset-0 flex items-center justify-center text-xs text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity">
                                                {{ $x }},{{ $y }}
                                            </span>
                                        </div>
                                    @endfor
                                @endfor
                            </div>
                        </div>
                    </div>

                    {{-- Легенда --}}
                    @if(count($zones) > 0)
                        <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                            <h3 class="text-sm font-medium text-gray-700 mb-2">Легенда зон:</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($zones as $zone)
                                    <button
                                        wire:click="selectZone({{ $zone['id'] }})"
                                        class="flex items-center space-x-2 px-3 py-1 rounded-full text-sm transition-all
                                               {{ $selectedZoneId === $zone['id'] ? 'ring-2 ring-gray-900' : 'hover:ring-2 hover:ring-gray-300' }}"
                                        style="background-color: {{ $zone['color'] }}; color: {{ $this->getContrastColor($zone['color']) }};"
                                    >
                                        <span class="font-medium">{{ $zone['name'] }}</span>
                                        <span class="opacity-75">×{{ $zone['price_coef'] }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Правая панель: Управление --}}
                <div class="xl:col-span-1 space-y-6">
                    {{-- Форма зоны --}}
                    @if($mode === 'draw' || $mode === 'edit')
                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                {{ $mode === 'edit' ? 'Редактирование зоны' : 'Новая зона' }}
                            </h3>

                            <form wire:submit.prevent="saveZone" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Название зоны
                                    </label>
                                    <input type="text" 
                                           wire:model="zoneName"
                                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="VIP зона">
                                    @error('zoneName') 
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p> 
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Цвет зоны
                                    </label>
                                    <div class="flex items-center space-x-2">
                                        <input type="color" 
                                               wire:model.live="zoneColor"
                                               class="h-10 w-20 border-gray-300 rounded cursor-pointer">
                                        <input type="text" 
                                               wire:model="zoneColor"
                                               class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"
                                               placeholder="#3B82F6">
                                    </div>
                                    @error('zoneColor') 
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p> 
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Коэффициент цены
                                    </label>
                                    <input type="number" 
                                           step="0.1"
                                           wire:model="zonePriceCoef"
                                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="1.0">
                                    @error('zonePriceCoef') 
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p> 
                                    @enderror
                                    <p class="text-xs text-gray-500 mt-1">
                                        1.0 = базовая цена, 1.5 = +50%, 0.8 = -20%
                                    </p>
                                </div>

                                <div class="bg-blue-50 p-3 rounded-lg">
                                    <p class="text-sm text-blue-800">
                                        <strong>Выбрано ячеек:</strong> {{ count($selectedCells) }}
                                    </p>
                                </div>

                                <div class="flex space-x-2">
                                    <button type="button"
                                            wire:click="clearSelection"
                                            class="flex-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md transition">
                                        Очистить выделение
                                    </button>
                                </div>

                                <div class="flex space-x-2 pt-2">
                                    <button type="button"
                                            wire:click="cancelDrawing"
                                            class="flex-1 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md transition">
                                        Отмена
                                    </button>
                                    <button type="submit"
                                            class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition">
                                        {{ $mode === 'edit' ? 'Обновить' : 'Создать' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    @else
                        {{-- Список зон --}}
                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    Зоны ({{ count($zones) }})
                                </h3>
                                <button wire:click="startDrawing"
                                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-md transition">
                                    + Создать зону
                                </button>
                            </div>

                            @if(count($zones) === 0)
                                <div class="text-center py-8">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500">Зоны не созданы</p>
                                    <p class="text-xs text-gray-400">Нажмите "Создать зону" для начала</p>
                                </div>
                            @else
                                <div class="space-y-2">
                                    @foreach($zones as $zone)
                                        <div class="border border-gray-200 rounded-lg p-3 hover:border-gray-300 transition
                                                    {{ $selectedZoneId === $zone['id'] ? 'ring-2 ring-blue-500' : '' }}">
                                            <div class="flex items-start justify-between">
                                                <div class="flex items-start space-x-3 flex-1">
                                                    <div class="w-6 h-6 rounded flex-shrink-0 border border-gray-300"
                                                         style="background-color: {{ $zone['color'] }};">
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <h4 class="text-sm font-medium text-gray-900 truncate">
                                                            {{ $zone['name'] }}
                                                        </h4>
                                                        <p class="text-xs text-gray-500 mt-1">
                                                            Коэффициент: ×{{ $zone['price_coef'] }}
                                                        </p>
                                                        <p class="text-xs text-gray-400">
                                                            Ячеек: {{ count($zone['coordinates']) }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="flex items-center space-x-1 ml-2">
                                                    <button wire:click="editZone({{ $zone['id'] }})"
                                                            class="p-1.5 text-blue-600 hover:bg-blue-50 rounded transition"
                                                            title="Редактировать">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                    </button>
                                                    <button wire:click="deleteZone({{ $zone['id'] }})"
                                                            wire:confirm="Удалить зону '{{ $zone['name'] }}'?"
                                                            class="p-1.5 text-red-600 hover:bg-red-50 rounded transition"
                                                            title="Удалить">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- Подсказки --}}
                        <div class="bg-blue-50 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-blue-900 mb-2">💡 Подсказки</h4>
                            <ul class="text-xs text-blue-800 space-y-1">
                                <li>• Нажмите на зону в списке, чтобы подсветить на карте</li>
                                <li>• Создайте зоны с разными коэффициентами для гибкого ценообразования</li>
                                <li>• Используйте яркие цвета для лучшей визуализации</li>
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Выберите заведение</h3>
                <p class="mt-2 text-sm text-gray-500">Для начала работы выберите заведение из списка выше</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    // Вспомогательная функция для определения контрастного цвета текста
    Livewire.hook('morph.updated', () => {
        console.log('ZoneEditor updated');
    });
</script>
@endpush