<div class="max-w-full" x-data="hallEditorData()">
    {{-- Flash сообщения --}}
    @if(session()->has('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
            ✓ {{ session('success') }}
        </div>
    @endif
    
    @if(session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            ✕ {{ session('error') }}
        </div>
    @endif

    <div class="mb-6">
        <h1 class="text-3xl font-bold">🗺️ Редактор зала</h1>
        <p class="text-gray-600">Выберите место и расставьте столы на плане зала</p>
    </div>

    {{-- Выбор места --}}
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block font-medium mb-2">Заведение</label>
                <select wire:model.live="placeId" class="w-full border rounded-lg px-3 py-2">
                    <option value="">-- Выберите место --</option>
                    @foreach($places as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            @if($place)
            <div>
                <label class="block font-medium mb-2">Режим</label>
                <div class="flex space-x-2">
                    <button wire:click="$set('mode', 'tables')" 
                            class="flex-1 px-4 py-2 rounded-lg transition {{ $mode === 'tables' ? 'bg-blue-500 text-white' : 'bg-gray-200 hover:bg-gray-300' }}">
                        🪑 Столы
                    </button>
                    <button wire:click="$set('mode', 'zones')" 
                            class="flex-1 px-4 py-2 rounded-lg transition {{ $mode === 'zones' ? 'bg-blue-500 text-white' : 'bg-gray-200 hover:bg-gray-300' }}">
                        🎨 Зоны
                    </button>
                </div>
            </div>

            <div>
                <label class="block font-medium mb-2">Размер сетки</label>
                <div class="flex space-x-2">
                    <input type="number" wire:model.defer="gridWidth" 
                           class="w-20 border rounded px-2 py-2" min="5" max="50">
                    <span class="py-2">×</span>
                    <input type="number" wire:model.defer="gridHeight" 
                           class="w-20 border rounded px-2 py-2" min="5" max="50">
                    <button wire:click="updateGridSize" 
                            class="px-3 py-2 bg-green-500 hover:bg-green-600 text-white rounded transition">
                        ✓
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>

    @if(!$place)
        <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg p-12 text-center">
            <div class="text-6xl mb-4">🏢</div>
            <p class="text-xl text-gray-600">Выберите заведение, чтобы начать редактирование</p>
        </div>
    @else
        {{-- Режим редактирования столов --}}
        @if($mode === 'tables')
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            
            {{-- Контейнер со столами --}}
            <div class="bg-white rounded-lg shadow-md p-4">
                <h3 class="font-bold mb-3 flex items-center justify-between">
                    <span>📦 Столы</span>
                    <span class="text-sm text-gray-500">
                        {{ count(array_filter($resources, fn($r) => !$r['on_grid'])) }}
                    </span>
                </h3>
                
                <div class="space-y-2 max-h-[600px] overflow-y-auto" id="tables-container">
                    @forelse(array_filter($resources, fn($r) => !$r['on_grid']) as $table)
                        <div wire:click="selectTableFromContainer({{ $table['id'] }})"
                             class="table-item p-3 border-2 rounded-lg cursor-pointer transition
                                    {{ $selectedTableId == $table['id'] ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-blue-300 hover:shadow' }}"
                             data-resource-id="{{ $table['id'] }}"
                             data-width="{{ $table['grid_width'] }}"
                             data-height="{{ $table['grid_height'] }}">
                            <div class="font-bold">{{ $table['code'] }}</div>
                            <div class="text-xs text-gray-600">{{ $table['model_name'] }}</div>
                            <div class="text-xs text-gray-500">Размер: {{ $table['grid_width'] }}×{{ $table['grid_height'] }}</div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-4">✓ Все столы на карте</p>
                    @endforelse
                </div>

                <div class="mt-4 p-3 bg-blue-50 rounded text-xs">
                    <strong>💡 Подсказка:</strong>
                    <ul class="mt-2 space-y-1">
                        <li>1️⃣ Нажмите на стол</li>
                        <li>2️⃣ Кликните на сетку</li>
                        <li>3️⃣ Или перетащите мышкой</li>
                    </ul>
                </div>
            </div>

            {{-- Сетка зала --}}
            <div class="lg:col-span-3 bg-white rounded-lg shadow-md p-6">
                <div class="mb-4 flex justify-between items-center">
                    <h3 class="font-bold">🗺️ План зала ({{ $gridWidth }}×{{ $gridHeight }})</h3>
                    <div class="text-sm text-gray-600">
                        <span class="inline-block px-2 py-1 bg-green-100 rounded">
                            На карте: {{ count(array_filter($resources, fn($r) => $r['on_grid'])) }}
                        </span>
                    </div>
                </div>

                <div class="border-2 border-gray-300 rounded-lg overflow-auto bg-gray-50" 
                     style="max-height: 70vh;">
                    
                    <div id="grid-canvas" 
                         class="relative"
                         style="width: {{ $gridWidth * $cellSize }}px; 
                                height: {{ $gridHeight * $cellSize }}px;"
                         data-grid-width="{{ $gridWidth }}"
                         data-grid-height="{{ $gridHeight }}"
                         data-cell-size="{{ $cellSize }}">
                        
                        {{-- SVG сетка --}}
                        <svg class="absolute inset-0 pointer-events-none" width="100%" height="100%">
                            @for($x = 0; $x <= $gridWidth; $x++)
                                <line x1="{{ $x * $cellSize }}" y1="0" 
                                      x2="{{ $x * $cellSize }}" y2="{{ $gridHeight * $cellSize }}" 
                                      stroke="#e5e7eb" stroke-width="1"/>
                                @if($x % 5 === 0)
                                    <text x="{{ $x * $cellSize + 2 }}" y="12" 
                                          font-size="10" fill="#9ca3af">{{ $x }}</text>
                                @endif
                            @endfor
                            @for($y = 0; $y <= $gridHeight; $y++)
                                <line x1="0" y1="{{ $y * $cellSize }}" 
                                      x2="{{ $gridWidth * $cellSize }}" y2="{{ $y * $cellSize }}" 
                                      stroke="#e5e7eb" stroke-width="1"/>
                                @if($y % 5 === 0)
                                    <text x="2" y="{{ $y * $cellSize + 12 }}" 
                                          font-size="10" fill="#9ca3af">{{ $y }}</text>
                                @endif
                            @endfor
                        </svg>

                        {{-- Зоны (подложка) --}}
                        @foreach($zones as $zone)
                            @if(!empty($zone['coordinates']))
                            <svg class="absolute inset-0 pointer-events-none" width="100%" height="100%">
                                <polygon 
                                    points="{{ collect($zone['coordinates'])->map(fn($p) => ($p['x'] * $cellSize) . ',' . ($p['y'] * $cellSize))->join(' ') }}"
                                    fill="{{ $zone['color'] }}"
                                    opacity="0.2"
                                    stroke="{{ $zone['color'] }}"
                                    stroke-width="2"
                                />
                                <text 
                                    x="{{ collect($zone['coordinates'])->avg('x') * $cellSize }}" 
                                    y="{{ collect($zone['coordinates'])->avg('y') * $cellSize }}"
                                    text-anchor="middle"
                                    font-size="14"
                                    fill="{{ $zone['color'] }}"
                                    font-weight="bold">
                                    {{ $zone['name'] }}
                                </text>
                            </svg>
                            @endif
                        @endforeach

                        {{-- Столы на сетке --}}
                        @foreach(array_filter($resources, fn($r) => $r['on_grid']) as $table)
                            <div class="resource-on-grid absolute cursor-move rounded-lg flex items-center justify-center font-bold text-white text-sm shadow-lg transition-all hover:shadow-xl
                                        {{ $table['state'] === 'available' ? 'bg-green-500 hover:bg-green-600' : 'bg-yellow-500 hover:bg-yellow-600' }}"
                                 data-resource-id="{{ $table['id'] }}"
                                 data-width="{{ $table['grid_width'] }}"
                                 data-height="{{ $table['grid_height'] }}"
                                 style="left: {{ $table['grid_x'] * $cellSize }}px; 
                                        top: {{ $table['grid_y'] * $cellSize }}px; 
                                        width: {{ $table['grid_width'] * $cellSize }}px; 
                                        height: {{ $table['grid_height'] * $cellSize }}px;
                                        transform: rotate({{ $table['rotation'] }}deg);
                                        transform-origin: center;">
                                <div class="text-center">
                                    <div>{{ $table['code'] }}</div>
                                    <div class="text-xs opacity-90">{{ $table['zone_name'] }}</div>
                                </div>
                                
                                {{-- Контекстное меню --}}
                                <div class="absolute -top-1 -right-1 flex opacity-0 hover:opacity-100 transition-opacity">
                                    <button wire:click="rotateTable({{ $table['id'] }})"
                                            onclick="event.stopPropagation();"
                                            class="bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded-l text-xs transition">
                                        🔄
                                    </button>
                                    <button wire:click="removeTableFromGrid({{ $table['id'] }})"
                                            onclick="event.stopPropagation(); if(!confirm('Убрать стол с карты?')) event.preventDefault();"
                                            class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded-r text-xs transition">
                                        ✕
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Подсказки --}}
                <div class="mt-4 grid grid-cols-2 gap-4">
                    <div class="p-3 bg-blue-50 rounded text-sm">
                        <strong>⌨️ Управление:</strong>
                        <ul class="mt-2 space-y-1 text-xs">
                            <li>• Drag & Drop - перемещение</li>
                            <li>• 🔄 - повернуть на 90°</li>
                            <li>• ✕ - убрать с карты</li>
                        </ul>
                    </div>
                    
                    <div class="p-3 bg-gray-50 rounded text-sm">
                        <strong>🎨 Легенда:</strong>
                        <div class="mt-2 space-y-1 text-xs">
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-green-500 rounded mr-2"></div>
                                <span>Доступен</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-yellow-500 rounded mr-2"></div>
                                <span>На ремонте</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Режим редактирования зон --}}
        @if($mode === 'zones')
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            
            {{-- Панель управления зонами --}}
            <div class="bg-white rounded-lg shadow-md p-4">
                <h3 class="font-bold mb-3">🎨 Зоны</h3>
                
                @if(!$drawingZone)
                    <button wire:click="startDrawingZone" 
                            class="w-full px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg mb-4 transition">
                        ➕ Создать зону
                    </button>
                @else
                    <div class="p-3 bg-yellow-50 border border-yellow-300 rounded mb-4">
                        <p class="text-sm font-medium mb-2">Рисуем зону...</p>
                        <p class="text-xs text-gray-600 mb-2">Точек: {{ count($zonePoints) }}</p>
                        <button wire:click="cancelZoneDrawing"
                                class="w-full px-3 py-1 bg-gray-500 hover:bg-gray-600 text-white rounded text-sm transition">
                            Отменить
                        </button>
                    </div>
                @endif
                
                <div class="space-y-2 max-h-[500px] overflow-y-auto">
                    @forelse($zones as $zone)
                        <div class="p-3 border-2 rounded-lg {{ $editingZoneId == $zone['id'] ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center">
                                    <div class="w-4 h-4 rounded mr-2" style="background: {{ $zone['color'] }}"></div>
                                    <div class="font-medium">{{ $zone['name'] }}</div>
                                </div>
                            </div>
                            <div class="text-xs text-gray-600">
                                Коэффициент: {{ $zone['price_coef'] }}
                            </div>
                            <div class="mt-2 flex space-x-1">
                                <button wire:click="deleteZone({{ $zone['id'] }})"
                                        onclick="if(!confirm('Удалить зону?')) event.preventDefault();"
                                        class="text-xs px-2 py-1 bg-red-500 hover:bg-red-600 text-white rounded transition">
                                    Удалить
                                </button>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-4">Нет зон</p>
                    @endforelse
                </div>
            </div>

            {{-- Канвас для рисования зон --}}
            <div class="lg:col-span-3 bg-white rounded-lg shadow-md p-6">
                <div class="mb-4">
                    <h3 class="font-bold">🗺️ План зала ({{ $gridWidth }}×{{ $gridHeight }})</h3>
                    @if($drawingZone)
                        <p class="text-sm text-yellow-600 mt-2">
                            Кликайте по сетке, чтобы добавить точки зоны. Минимум 3 точки.
                        </p>
                    @endif
                </div>

                <div class="border-2 border-gray-300 rounded-lg overflow-auto bg-gray-50" 
                     style="max-height: 70vh;">
                    
                    <div id="zone-canvas" 
                         class="relative {{ $drawingZone ? 'cursor-crosshair' : '' }}"
                         style="width: {{ $gridWidth * $cellSize }}px; 
                                height: {{ $gridHeight * $cellSize }}px;">
                        
                        {{-- SVG сетка --}}
                        <svg class="absolute inset-0 pointer-events-none" width="100%" height="100%">
                            @for($x = 0; $x <= $gridWidth; $x++)
                                <line x1="{{ $x * $cellSize }}" y1="0" 
                                      x2="{{ $x * $cellSize }}" y2="{{ $gridHeight * $cellSize }}" 
                                      stroke="#e5e7eb" stroke-width="1"/>
                            @endfor
                            @for($y = 0; $y <= $gridHeight; $y++)
                                <line x1="0" y1="{{ $y * $cellSize }}" 
                                      x2="{{ $gridWidth * $cellSize }}" y2="{{ $y * $cellSize }}" 
                                      stroke="#e5e7eb" stroke-width="1"/>
                            @endfor
                        </svg>

                        {{-- Существующие зоны --}}
                        @foreach($zones as $zone)
                            @if(!empty($zone['coordinates']))
                            <svg class="absolute inset-0 pointer-events-none" width="100%" height="100%">
                                <polygon 
                                    points="{{ collect($zone['coordinates'])->map(fn($p) => ($p['x'] * $cellSize) . ',' . ($p['y'] * $cellSize))->join(' ') }}"
                                    fill="{{ $zone['color'] }}"
                                    opacity="0.3"
                                    stroke="{{ $zone['color'] }}"
                                    stroke-width="3"
                                />
                                <text 
                                    x="{{ collect($zone['coordinates'])->avg('x') * $cellSize }}" 
                                    y="{{ collect($zone['coordinates'])->avg('y') * $cellSize }}"
                                    text-anchor="middle"
                                    font-size="16"
                                    fill="{{ $zone['color'] }}"
                                    font-weight="bold">
                                    {{ $zone['name'] }}
                                </text>
                            </svg>
                            @endif
                        @endforeach

                        {{-- Рисуемая зона (в процессе) --}}
                        @if($drawingZone && count($zonePoints) > 0)
                        <svg class="absolute inset-0 pointer-events-none" width="100%" height="100%">
                            @if(count($zonePoints) > 1)
                                <polyline 
                                    points="{{ collect($zonePoints)->map(fn($p) => ($p['x'] * $cellSize) . ',' . ($p['y'] * $cellSize))->join(' ') }}"
                                    fill="none"
                                    stroke="#3B82F6"
                                    stroke-width="2"
                                    stroke-dasharray="5,5"
                                />
                            @endif
                            @foreach($zonePoints as $point)
                                <circle 
                                    cx="{{ $point['x'] * $cellSize }}" 
                                    cy="{{ $point['y'] * $cellSize }}" 
                                    r="5" 
                                    fill="#3B82F6"
                                />
                            @endforeach
                        </svg>
                        @endif
                    </div>
                </div>

                {{-- Форма создания зоны --}}
                @if($drawingZone && count($zonePoints) >= 3)
                <div class="mt-4 p-4 bg-blue-50 border border-blue-300 rounded-lg">
                    <h4 class="font-bold mb-3">Завершить зону</h4>
                    <div class="grid grid-cols-3 gap-3">
                        <input type="text" x-model="zoneName" placeholder="Название" 
                               class="border rounded px-3 py-2" />
                        <input type="color" x-model="zoneColor" 
                               class="border rounded px-3 py-2" />
                        <input type="number" x-model="zonePriceCoef" placeholder="Коэф." 
                               class="border rounded px-3 py-2" step="0.1" min="0.5" max="5" />
                    </div>
                    <button @click="finishZone()"
                            class="mt-3 w-full px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition">
                        ✓ Создать зону
                    </button>
                </div>
                @endif
            </div>
        </div>
        @endif
    @endif
</div>

@push('scripts')
<script>
function hallEditorData() {
    return {
        zoneName: 'Новая зона',
        zoneColor: '#3B82F6',
        zonePriceCoef: 1.0,
        
        finishZone() {
            @this.call('finishZone', this.zoneName, this.zoneColor, this.zonePriceCoef);
            this.zoneName = 'Новая зона';
            this.zoneColor = '#3B82F6';
            this.zonePriceCoef = 1.0;
        }
    };
}

const cellSize = {{ $cellSize }};
let selectedTableId = @json($selectedTableId);
let draggedElement = null;
let isDragging = false;
let isDrawingZone = @json($drawingZone);

// Обновление выбранного стола из Livewire
document.addEventListener('livewire:init', () => {
    Livewire.on('table-selected', (event) => {
        selectedTableId = event.resourceId;
    });
});

// Клик на сетку для размещения выбранного стола
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('grid-canvas');
    const zoneCanvas = document.getElementById('zone-canvas');
    
    if (canvas) {
        // Клик для размещения стола
        canvas.addEventListener('click', function(e) {
            if (!selectedTableId) return;
            if (isDragging) return;
            
            const rect = canvas.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const gridX = Math.floor(x / cellSize);
            const gridY = Math.floor(y / cellSize);
            
            @this.call('placeTableOnGrid', selectedTableId, gridX, gridY);
            selectedTableId = null;
        });
        
        // Drag & Drop для столов НА сетке
        canvas.addEventListener('mousedown', function(e) {
            const target = e.target.closest('.resource-on-grid');
            if (!target) return;
            
            e.preventDefault();
            draggedElement = target;
            isDragging = false;
            
            const rect = target.getBoundingClientRect();
            const canvasRect = canvas.getBoundingClientRect();
            
            draggedElement.offsetX = e.clientX - rect.left;
            draggedElement.offsetY = e.clientY - rect.top;
            draggedElement.style.zIndex = '1000';
            draggedElement.style.cursor = 'grabbing';
        });
        
        document.addEventListener('mousemove', function(e) {
            if (!draggedElement) return;
            isDragging = true;
            
            const canvasRect = canvas.getBoundingClientRect();
            let x = e.clientX - canvasRect.left - draggedElement.offsetX;
            let y = e.clientY - canvasRect.top - draggedElement.offsetY;
            
            // Snap to grid
            let gridX = Math.round(x / cellSize);
            let gridY = Math.round(y / cellSize);
            
            const width = parseInt(draggedElement.dataset.width);
            const height = parseInt(draggedElement.dataset.height);
            const gridWidth = parseInt(canvas.dataset.gridWidth);
            const gridHeight = parseInt(canvas.dataset.gridHeight);
            
            // Границы
            if (gridX < 0) gridX = 0;
            if (gridY < 0) gridY = 0;
            if (gridX + width > gridWidth) gridX = gridWidth - width;
            if (gridY + height > gridHeight) gridY = gridHeight - height;
            
            draggedElement.style.left = (gridX * cellSize) + 'px';
            draggedElement.style.top = (gridY * cellSize) + 'px';
        });
        
        document.addEventListener('mouseup', function(e) {
            if (!draggedElement) return;
            
            if (isDragging) {
                const gridX = Math.round(parseInt(draggedElement.style.left) / cellSize);
                const gridY = Math.round(parseInt(draggedElement.style.top) / cellSize);
                const resourceId = draggedElement.dataset.resourceId;
                
                @this.call('updateTablePosition', resourceId, gridX, gridY);
            }
            
            draggedElement.style.zIndex = '';
            draggedElement.style.cursor = 'move';
            draggedElement = null;
            
            setTimeout(() => { isDragging = false; }, 100);
        });
    }
    
    // Клик для рисования зон
    if (zoneCanvas) {
        zoneCanvas.addEventListener('click', function(e) {
            if (!isDrawingZone) return;
            
            const rect = zoneCanvas.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const gridX = Math.round(x / cellSize);
            const gridY = Math.round(y / cellSize);
            
            @this.call('addZonePoint', gridX, gridY);
        });
    }
});

// Обновление состояния рисования зоны
Livewire.hook('morph.updated', () => {
    isDrawingZone = @json($drawingZone);
});
</script>
@endpush