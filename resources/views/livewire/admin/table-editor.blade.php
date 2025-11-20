<div class="min-h-screen bg-gray-50 p-4">
    <div class="max-w-screen-2xl mx-auto">
        {{-- Заголовок --}}
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Редактор столов</h1>
            <p class="text-gray-600 mt-1">Размещайте столы на карте зала с автоматическим определением зон</p>
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

        @if (session()->has('warning'))
            <div class="mb-4 bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg">
                {{ session('warning') }}
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
                {{-- Левая панель: Карта --}}
                <div class="xl:col-span-3 bg-white rounded-lg shadow-sm p-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-xl font-semibold text-gray-900">
                            Карта зала ({{ $gridWidth }}×{{ $gridHeight }})
                        </h2>
                        
                        {{-- Индикатор --}}
                        <div class="flex items-center space-x-2">
                            @if($selectedTableId)
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full animate-pulse">
                                    📍 Кликните на карту для размещения
                                </span>
                            @elseif($selectedGridTableId)
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-sm font-medium rounded-full">
                                    ✏️ Стол выбран
                                </span>
                            @else
                                <span class="px-3 py-1 bg-gray-100 text-gray-800 text-sm font-medium rounded-full">
                                    👆 Кликните на стол для управления
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Сетка с зонами и столами --}}
                    <div class="border-2 border-gray-300 rounded-lg overflow-auto bg-gray-50" 
                         style="max-height: calc(100vh - 300px);">
                        <div class="inline-block min-w-full p-4">
                            <div class="relative grid gap-0" 
                                 style="grid-template-columns: repeat({{ $gridWidth }}, 1fr); 
                                        grid-template-rows: repeat({{ $gridHeight }}, 1fr);">
                                
                                {{-- Ячейки сетки с зонами --}}
                                @for($y = 0; $y < $gridHeight; $y++)
                                    @for($x = 0; $x < $gridWidth; $x++)
                                        @php
                                            // Определяем зону ячейки
                                            $cellZone = null;
                                            foreach($zones as $zone) {
                                                if(in_array(['x' => $x, 'y' => $y], $zone['coordinates'])) {
                                                    $cellZone = $zone;
                                                    break;
                                                }
                                            }
                                        @endphp
                                        
                                        <div
                                            wire:click="placeTable({{ $x }}, {{ $y }})"
                                            data-cell-x="{{ $x }}"
                                            data-cell-y="{{ $y }}"
                                            class="aspect-square border border-gray-200 transition-all duration-150
                                                   {{ $selectedTableId ? 'cursor-pointer hover:border-blue-500 hover:bg-blue-50' : '' }}
                                                   relative group"
                                            style="
                                                background-color: {{ $cellZone ? $cellZone['color'] : 'white' }};
                                                opacity: {{ $cellZone ? '0.3' : '1' }};
                                                min-width: 50px;
                                                min-height: 50px;
                                            "
                                        >
                                            {{-- Координаты при наведении --}}
                                            <span class="absolute inset-0 flex items-center justify-center text-xs text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10">
                                                {{ $x }},{{ $y }}
                                            </span>
                                        </div>
                                    @endfor
                                @endfor

                                {{-- Столы поверх сетки --}}
                                @foreach($tablesOnGrid as $table)
                                    @php
                                        // Вычисляем размеры с учетом поворота
                                        $displayWidth = ($table['rotation'] === 90 || $table['rotation'] === 270) 
                                            ? $table['grid_height'] 
                                            : $table['grid_width'];
                                        $displayHeight = ($table['rotation'] === 90 || $table['rotation'] === 270) 
                                            ? $table['grid_width'] 
                                            : $table['grid_height'];
                                        
                                        $isSelected = $selectedGridTableId === $table['id'];
                                    @endphp
                                    
                                    <div
                                        wire:click.stop="selectGridTable({{ $table['id'] }})"
                                        data-table-id="{{ $table['id'] }}"
                                        draggable="true"
                                        class="absolute flex items-center justify-center
                                               border-2 rounded-lg cursor-move transition-all duration-200
                                               {{ $isSelected ? 'border-yellow-500 bg-yellow-100 shadow-lg ring-4 ring-yellow-200 z-20' : 'border-gray-800 bg-white hover:border-blue-500 hover:shadow-lg z-10' }}"
                                        style="
                                            grid-column: {{ $table['grid_x'] + 1 }} / span {{ $displayWidth }};
                                            grid-row: {{ $table['grid_y'] + 1 }} / span {{ $displayHeight }};
                                            background-color: {{ $table['zone_color'] }}20;
                                            transform: rotate({{ $table['rotation'] }}deg);
                                            transform-origin: center center;
                                        "
                                    >
                                        <div class="text-center px-2 py-1 pointer-events-none">
                                            <div class="text-sm font-bold text-gray-900">
                                                {{ $table['code'] }}
                                            </div>
                                            <div class="text-xs text-gray-600 truncate">
                                                {{ $table['model_name'] }}
                                            </div>
                                            @if($table['rotation'] > 0)
                                                <div class="text-xs text-blue-600 font-medium mt-1">
                                                    ↻ {{ $table['rotation'] }}°
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Легенда зон --}}
                    @if(count($zones) > 0)
                        <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                            <h3 class="text-sm font-medium text-gray-700 mb-2">Зоны:</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($zones as $zone)
                                    <div class="flex items-center space-x-2 px-3 py-1 rounded-full text-sm"
                                         style="background-color: {{ $zone['color'] }}40; border: 2px solid {{ $zone['color'] }};">
                                        <span class="font-medium">{{ $zone['name'] }}</span>
                                        <span class="opacity-75">×{{ $zone['price_coef'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Правая панель: Управление столами --}}
                <div class="xl:col-span-1 space-y-6">
                    {{-- Действия с выбранным столом на карте --}}
                    @if($selectedGridTableId)
                        @php
                            $selectedTable = collect($tablesOnGrid)->firstWhere('id', $selectedGridTableId);
                        @endphp
                        
                        @if($selectedTable)
                            <div class="bg-yellow-50 border-2 border-yellow-300 rounded-lg shadow-sm p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                    <span class="text-2xl mr-2">📌</span>
                                    Управление столом
                                </h3>

                                <div class="space-y-3 mb-4">
                                    <div class="bg-white rounded p-3">
                                        <div class="text-sm text-gray-600">Код:</div>
                                        <div class="font-bold text-lg">{{ $selectedTable['code'] }}</div>
                                    </div>
                                    
                                    <div class="bg-white rounded p-3">
                                        <div class="text-sm text-gray-600">Модель:</div>
                                        <div class="font-medium">{{ $selectedTable['model_name'] }}</div>
                                    </div>
                                    
                                    <div class="bg-white rounded p-3">
                                        <div class="text-sm text-gray-600">Зона:</div>
                                        <div class="flex items-center space-x-2">
                                            <div class="w-4 h-4 rounded border border-gray-300"
                                                 style="background-color: {{ $selectedTable['zone_color'] }};"></div>
                                            <span class="font-medium">{{ $selectedTable['zone_name'] }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="bg-white rounded p-3">
                                        <div class="text-sm text-gray-600">Позиция:</div>
                                        <div class="font-mono">({{ $selectedTable['grid_x'] }}, {{ $selectedTable['grid_y'] }})</div>
                                    </div>
                                    
                                    <div class="bg-white rounded p-3">
                                        <div class="text-sm text-gray-600">Поворот:</div>
                                        <div class="font-mono">{{ $selectedTable['rotation'] }}°</div>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <button wire:click="rotateTable({{ $selectedTable['id'] }})"
                                            class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition flex items-center justify-center space-x-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        <span>Повернуть на 90°</span>
                                    </button>
                                    
                                    <button wire:click="removeTable({{ $selectedTable['id'] }})"
                                            wire:confirm="Убрать стол '{{ $selectedTable['code'] }}' с карты?"
                                            class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md transition flex items-center justify-center space-x-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        <span>Убрать с карты</span>
                                    </button>
                                </div>

                                <div class="mt-4 pt-4 border-t border-yellow-200">
                                    <p class="text-xs text-gray-600">
                                        💡 Перетаскивайте стол мышкой для перемещения
                                    </p>
                                </div>
                            </div>
                        @endif
                    @endif

                    {{-- Доступные столы --}}
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Доступные столы ({{ count($tablesAvailable) }})
                            </h3>
                        </div>

                        @if(count($tablesAvailable) === 0)
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">Все столы размещены</p>
                                <p class="text-xs text-gray-400">Отлично! 🎉</p>
                            </div>
                        @else
                            <div class="space-y-2 max-h-96 overflow-y-auto">
                                @foreach($tablesAvailable as $table)
                                    <button
                                        wire:click="selectTable({{ $table['id'] }})"
                                        class="w-full text-left border rounded-lg p-3 transition-all
                                               {{ $selectedTableId === $table['id'] ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-200' : 'border-gray-200 hover:border-blue-300 hover:bg-blue-50' }}"
                                    >
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <div class="font-bold text-gray-900">
                                                    {{ $table['code'] }}
                                                </div>
                                                <div class="text-sm text-gray-600">
                                                    {{ $table['model_name'] }}
                                                </div>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    Размер: {{ $table['grid_width'] }}×{{ $table['grid_height'] }}
                                                </div>
                                            </div>
                                            
                                            @if($selectedTableId === $table['id'])
                                                <div class="ml-2">
                                                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Столы на карте --}}
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            На карте ({{ count($tablesOnGrid) }})
                        </h3>

                        @if(count($tablesOnGrid) === 0)
                            <div class="text-center py-8">
                                <p class="text-sm text-gray-500">Столы не размещены</p>
                                <p class="text-xs text-gray-400 mt-1">Выберите стол и кликните на карту</p>
                            </div>
                        @else
                            <div class="space-y-2 max-h-64 overflow-y-auto">
                                @foreach($tablesOnGrid as $table)
                                    <div class="border border-gray-200 rounded-lg p-2 text-sm">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1 min-w-0">
                                                <div class="font-medium text-gray-900 truncate">
                                                    {{ $table['code'] }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    ({{ $table['grid_x'] }}, {{ $table['grid_y'] }}) • {{ $table['zone_name'] }}
                                                </div>
                                            </div>
                                            <button 
                                                wire:click="selectGridTable({{ $table['id'] }})"
                                                class="ml-2 p-1 text-blue-600 hover:bg-blue-50 rounded"
                                                title="Выбрать"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </button>
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
                            <li>• Выберите стол из списка и кликните на карту</li>
                            <li>• Перетаскивайте столы мышкой для перемещения</li>
                            <li>• Зона определяется автоматически по позиции</li>
                            <li>• Поворот изменяет размеры стола</li>
                        </ul>
                    </div>
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
    let draggedTableId = null;
    let draggedTableElement = null;
    let gridElement = null;

    document.addEventListener('DOMContentLoaded', function() {
        // Получаем элемент сетки
        gridElement = document.querySelector('[style*="grid-template-columns"]');
        
        if (!gridElement) return;
        
        // Обработчик для начала перетаскивания
        document.addEventListener('dragstart', function(event) {
            if (!event.target.hasAttribute('draggable')) return;
            
            draggedTableElement = event.target;
            draggedTableId = event.target.getAttribute('data-table-id');
            
            if (draggedTableId) {
                event.target.style.opacity = '0.5';
                event.dataTransfer.effectAllowed = 'move';
            }
        });
        
        // Обработчик для окончания перетаскивания
        document.addEventListener('dragend', function(event) {
            if (draggedTableElement) {
                draggedTableElement.style.opacity = '1';
                draggedTableElement = null;
                draggedTableId = null;
            }
        });
        
        // Разрешаем drop на ячейках
        document.addEventListener('dragover', function(event) {
            if (!draggedTableId) return;
            
            const target = event.target;
            
            // Проверяем, что это ячейка сетки
            if (target.hasAttribute('data-cell-x')) {
                event.preventDefault();
                event.dataTransfer.dropEffect = 'move';
            }
        });
        
        // Обработчик drop
        document.addEventListener('drop', function(event) {
            if (!draggedTableId) return;
            
            const target = event.target;
            
            // Проверяем, что это ячейка сетки
            if (target.hasAttribute('data-cell-x')) {
                event.preventDefault();
                
                const x = parseInt(target.getAttribute('data-cell-x'));
                const y = parseInt(target.getAttribute('data-cell-y'));
                
                // Вызываем Livewire метод
                @this.call('moveTable', parseInt(draggedTableId), x, y);
            }
        });
    });
</script>
@endpush