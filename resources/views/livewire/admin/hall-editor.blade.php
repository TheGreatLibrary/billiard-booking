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
                    <div class="text-sm">
                        <span class="inline-block px-2 py-1 bg-green-100 rounded">
                            На карте: {{ count(array_filter($resources, fn($r) => $r['on_grid'])) }}
                        </span>
                        @if($selectedTableId)
                        <span class="inline-block px-2 py-1 bg-blue-500 text-white rounded ml-2 animate-pulse">
                            👆 Кликните на сетку чтобы разместить стол
                        </span>
                        @endif
                    </div>
                </div>

                <div class="border-2 border-gray-300 rounded-lg overflow-auto bg-gray-50" 
                     style="max-height: 70vh;">
                    
                    <div id="grid-canvas" 
                         class="relative bg-white cursor-crosshair"
                         style="width: {{ $gridWidth * $cellSize }}px; 
                                height: {{ $gridHeight * $cellSize }}px;
                                background-image: 
                                    repeating-linear-gradient(0deg, transparent, transparent {{ $cellSize - 1 }}px, #e5e7eb {{ $cellSize - 1 }}px, #e5e7eb {{ $cellSize }}px),
                                    repeating-linear-gradient(90deg, transparent, transparent {{ $cellSize - 1 }}px, #e5e7eb {{ $cellSize - 1 }}px, #e5e7eb {{ $cellSize }}px);
                                background-size: {{ $cellSize }}px {{ $cellSize }}px;"
                         data-grid-width="{{ $gridWidth }}"
                         data-grid-height="{{ $gridHeight }}"
                         data-cell-size="{{ $cellSize }}">
                        
                        {{-- Координатные метки --}}
                        @for($x = 0; $x <= $gridWidth; $x += 5)
                            <div class="absolute text-xs text-gray-400 pointer-events-none select-none" 
                                 style="left: {{ $x * $cellSize + 2 }}px; top: 2px; z-index: 1;">{{ $x }}</div>
                        @endfor
                        @for($y = 0; $y <= $gridHeight; $y += 5)
                            <div class="absolute text-xs text-gray-400 pointer-events-none select-none" 
                                 style="left: 2px; top: {{ $y * $cellSize + 2 }}px; z-index: 1;">{{ $y }}</div>
                        @endfor

                        {{-- Зоны (подложка) - один SVG для всех зон --}}
                        @if(count($zones) > 0)
                        <svg class="absolute inset-0 pointer-events-none select-none" 
                             style="z-index: 2;"
                             width="{{ $gridWidth * $cellSize }}" 
                             height="{{ $gridHeight * $cellSize }}">
                            @foreach($zones as $zone)
                                @if(!empty($zone['coordinates']))
                                <g>
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
                                </g>
                                @endif
                            @endforeach
                        </svg>
                        @endif

                        {{-- Столы на сетке --}}
                        @foreach(array_filter($resources, fn($r) => $r['on_grid']) as $table)
                            <div class="resource-on-grid absolute rounded-lg flex items-center justify-center font-bold text-white text-sm shadow-lg hover:shadow-xl
                                        {{ $table['state'] === 'available' ? 'bg-green-500 hover:bg-green-600' : 'bg-yellow-500 hover:bg-yellow-600' }}"
                                 data-resource-id="{{ $table['id'] }}"
                                 data-width="{{ $table['grid_width'] }}"
                                 data-height="{{ $table['grid_height'] }}"
                                 style="left: {{ $table['grid_x'] * $cellSize }}px; 
                                        top: {{ $table['grid_y'] * $cellSize }}px; 
                                        width: {{ $table['grid_width'] * $cellSize }}px; 
                                        height: {{ $table['grid_height'] * $cellSize }}px;
                                        transform: rotate({{ $table['rotation'] }}deg);
                                        transform-origin: center;
                                        z-index: 10;
                                        cursor: move;">
                                <div class="text-center pointer-events-none select-none">
                                    <div>{{ $table['code'] }}</div>
                                    <div class="text-xs opacity-90">{{ $table['zone_name'] }}</div>
                                </div>
                                
                                {{-- Контекстное меню - ИСПРАВЛЕНО: opacity 0.5 вместо 0 --}}
                                <div class="absolute -top-1 -right-1 flex opacity-50 hover:opacity-100 transition-opacity pointer-events-auto"
                                     style="z-index: 20;">
                                    <button wire:click="rotateTable({{ $table['id'] }})"
                                            type="button"
                                            class="bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded-l text-xs transition">
                                        🔄
                                    </button>
                                    <button wire:click="removeTableFromGrid({{ $table['id'] }})"
                                            type="button"
                                            onclick="if(!confirm('Убрать стол с карты?')) return false;"
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
                            👆 Кликайте по сетке, чтобы добавить точки зоны. Минимум 3 точки.
                        </p>
                    @endif
                </div>

                <div class="border-2 border-gray-300 rounded-lg overflow-auto bg-gray-50" 
                     style="max-height: 70vh;">
                    
                    <div id="zone-canvas" 
                         class="relative bg-white {{ $drawingZone ? 'cursor-crosshair' : '' }}"
                         style="width: {{ $gridWidth * $cellSize }}px; 
                                height: {{ $gridHeight * $cellSize }}px;
                                background-image: 
                                    repeating-linear-gradient(0deg, transparent, transparent {{ $cellSize - 1 }}px, #e5e7eb {{ $cellSize - 1 }}px, #e5e7eb {{ $cellSize }}px),
                                    repeating-linear-gradient(90deg, transparent, transparent {{ $cellSize - 1 }}px, #e5e7eb {{ $cellSize - 1 }}px, #e5e7eb {{ $cellSize }}px);
                                background-size: {{ $cellSize }}px {{ $cellSize }}px;">
                        
                        {{-- Существующие зоны --}}
                        @if(count($zones) > 0)
                        <svg class="absolute inset-0 pointer-events-none" 
                             width="{{ $gridWidth * $cellSize }}" 
                             height="{{ $gridHeight * $cellSize }}">
                            @foreach($zones as $zone)
                                @if(!empty($zone['coordinates']))
                                <g>
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
                                </g>
                                @endif
                            @endforeach
                        </svg>
                        @endif

                        {{-- Рисуемая зона (в процессе) --}}
                        @if($drawingZone && count($zonePoints) > 0)
                        <svg class="absolute inset-0 pointer-events-none" 
                             width="{{ $gridWidth * $cellSize }}" 
                             height="{{ $gridHeight * $cellSize }}">
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

document.addEventListener('DOMContentLoaded', function() {
    const cellSize = {{ $cellSize }};
    let selectedTableId = {{ $selectedTableId ?? 'null' }};
    let isDrawingZone = {{ $drawingZone ? 'true' : 'false' }};
    let draggedElement = null;
    let isDragging = false;
    let dragStartPos = null;
    let initialMousePos = null;

    console.log('🎯 Hall Editor JS инициализирован', { cellSize });

    // Обновление состояния при изменениях Livewire
    Livewire.hook('morph.updated', () => {
        selectedTableId = {{ $selectedTableId ?? 'null' }};
        isDrawingZone = {{ $drawingZone ? 'true' : 'false' }};
        console.log('🔄 State updated:', { selectedTableId, isDrawingZone });
    });

    // ==================== РЕЖИМ СТОЛОВ ====================
    const canvas = document.getElementById('grid-canvas');
    
    if (canvas) {
        console.log('✅ Grid canvas найден');
        
        // Клик по сетке для размещения выбранного стола
        canvas.addEventListener('click', function(e) {
            // Игнорируем клики по столам и их кнопкам
            if (e.target.closest('.resource-on-grid')) {
                console.log('⏭️ Клик по столу, игнорируем');
                return;
            }
            
            if (!selectedTableId) {
                console.log('⚠️ Стол не выбран');
                return;
            }
            
            if (isDragging) {
                console.log('⏭️ Идёт драг, игнорируем клик');
                return;
            }
            
            const rect = canvas.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const gridX = Math.floor(x / cellSize);
            const gridY = Math.floor(y / cellSize);
            
            console.log('📍 Размещаем стол:', { selectedTableId, gridX, gridY });
            
            @this.call('placeTableOnGrid', selectedTableId, gridX, gridY);
            selectedTableId = null;
        });
        
        // ==================== DRAG & DROP ====================
        // Используем делегирование событий через canvas
        
        canvas.addEventListener('mousedown', function(e) {
            // Находим элемент стола
            const target = e.target.closest('.resource-on-grid');
            if (!target) {
                console.log('⏭️ Клик не по столу');
                return;
            }
            
            // Игнорируем клики по кнопкам
            if (e.target.closest('button')) {
                console.log('⏭️ Клик по кнопке');
                return;
            }
            
            e.preventDefault();
            e.stopPropagation();
            
            console.log('🖱️ Mousedown на столе:', target.dataset.resourceId);
            
            draggedElement = target;
            isDragging = false;
            
            const rect = target.getBoundingClientRect();
            const canvasRect = canvas.getBoundingClientRect();
            
            // Сохраняем начальную позицию
            dragStartPos = {
                x: parseInt(target.style.left) || 0,
                y: parseInt(target.style.top) || 0
            };
            
            // Сохраняем позицию мыши относительно элемента
            initialMousePos = {
                offsetX: e.clientX - rect.left,
                offsetY: e.clientY - rect.top
            };
            
            // Визуальная обратная связь
            target.style.zIndex = '1000';
            target.style.opacity = '0.7';
            target.style.cursor = 'grabbing';
            
            console.log('✅ Drag начат', { dragStartPos, initialMousePos });
        });
        
        // Mousemove на document для плавного драга
        document.addEventListener('mousemove', function(e) {
            if (!draggedElement) return;
            
            isDragging = true;
            
            const canvasRect = canvas.getBoundingClientRect();
            
            // Вычисляем новую позицию
            let x = e.clientX - canvasRect.left - initialMousePos.offsetX;
            let y = e.clientY - canvasRect.top - initialMousePos.offsetY;
            
            // Snap to grid
            let gridX = Math.round(x / cellSize);
            let gridY = Math.round(y / cellSize);
            
            const width = parseInt(draggedElement.dataset.width);
            const height = parseInt(draggedElement.dataset.height);
            const gridWidth = parseInt(canvas.dataset.gridWidth);
            const gridHeight = parseInt(canvas.dataset.gridHeight);
            
            // Ограничения границ
            gridX = Math.max(0, Math.min(gridX, gridWidth - width));
            gridY = Math.max(0, Math.min(gridY, gridHeight - height));
            
            // Применяем позицию
            draggedElement.style.left = (gridX * cellSize) + 'px';
            draggedElement.style.top = (gridY * cellSize) + 'px';
        });
        
        // Mouseup на document
        document.addEventListener('mouseup', function(e) {
            if (!draggedElement) return;
            
            console.log('🖱️ Mouseup, isDragging:', isDragging);
            
            // Восстанавливаем стили
            draggedElement.style.zIndex = '10';
            draggedElement.style.opacity = '1';
            draggedElement.style.cursor = 'move';
            
            if (isDragging) {
                const gridX = Math.round(parseInt(draggedElement.style.left) / cellSize);
                const gridY = Math.round(parseInt(draggedElement.style.top) / cellSize);
                const resourceId = draggedElement.dataset.resourceId;
                
                console.log('💾 Сохраняем новую позицию:', { resourceId, gridX, gridY });
                
                // Сохраняем в базу
                @this.call('updateTablePosition', resourceId, gridX, gridY);
            } else {
                // Если не было движения, возвращаем на место
                console.log('⏮️ Возвращаем на место (не было движения)');
                draggedElement.style.left = dragStartPos.x + 'px';
                draggedElement.style.top = dragStartPos.y + 'px';
            }
            
            draggedElement = null;
            dragStartPos = null;
            initialMousePos = null;
            
            // Сброс флага с задержкой
            setTimeout(() => { 
                isDragging = false; 
                console.log('✅ Drag завершен');
            }, 100);
        });
        
        // Отмена драга при уходе мыши за пределы документа
        document.addEventListener('mouseleave', function(e) {
            if (draggedElement && e.target === document.body) {
                console.log('⚠️ Мышь покинула документ, отменяем драг');
                
                // Возвращаем на место
                draggedElement.style.left = dragStartPos.x + 'px';
                draggedElement.style.top = dragStartPos.y + 'px';
                draggedElement.style.zIndex = '10';
                draggedElement.style.opacity = '1';
                draggedElement.style.cursor = 'move';
                
                draggedElement = null;
                dragStartPos = null;
                initialMousePos = null;
                isDragging = false;
            }
        });
    }

    // ==================== РЕЖИМ ЗОН ====================
    const zoneCanvas = document.getElementById('zone-canvas');
    
    if (zoneCanvas) {
        console.log('✅ Zone canvas найден');
        
        zoneCanvas.addEventListener('click', function(e) {
            if (!isDrawingZone) {
                console.log('⚠️ Режим рисования зон не активен');
                return;
            }
            
            const rect = zoneCanvas.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const gridX = Math.round(x / cellSize);
            const gridY = Math.round(y / cellSize);
            
            console.log('📍 Добавляем точку зоны:', { gridX, gridY });
            
            @this.call('addZonePoint', gridX, gridY);
        });
    }
    
    console.log('🎉 Все обработчики событий установлены');
});
</script>
@endpush