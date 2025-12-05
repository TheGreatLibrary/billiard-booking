<div class="max-w-7xl mx-auto p-6">
    {{-- Flash сообщения --}}
    @if (session()->has('error'))
        <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-lg flex items-start">
            <svg class="w-5 h-5 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <span><strong>Ошибка:</strong> {{ session('error') }}</span>
        </div>
    @endif

    @if (session()->has('success'))
        <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-lg flex items-start">
            <svg class="w-5 h-5 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span><strong>Успех:</strong> {{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('warning'))
        <div class="mb-6 p-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 rounded-lg flex items-start">
            <svg class="w-5 h-5 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <span>{{ session('warning') }}</span>
        </div>
    @endif

    @if (session()->has('info'))
        <div class="mb-6 p-4 bg-blue-100 border-l-4 border-blue-500 text-blue-700 rounded-lg">
            {{ session('info') }}
        </div>
    @endif

    {{-- Прогресс шагов --}}
    <div class="mb-8">
        <div class="flex justify-between items-center">
            @foreach([1 => 'Место', 2 => 'Стол', 3 => 'Время', 4 => 'Доп. услуги', 5 => 'Данные', 6 => 'Оплата', 7 => 'Готово'] as $num => $name)
                <div class="flex items-center {{ $num < 7 ? 'flex-1' : '' }}">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold
                        {{ $step >= $num ? 'bg-blue-500 text-white' : 'bg-gray-300 text-gray-600' }}">
                        @if($num === 7 && $step === 7)
                            ✓
                        @else
                            {{ $num }}
                        @endif
                    </div>
                    <span class="ml-2 text-sm {{ $step >= $num ? 'text-blue-600 font-medium' : 'text-gray-500' }}">
                        {{ $name }}
                    </span>
                    @if($num < 7)
                        <div class="flex-1 h-1 mx-2 {{ $step > $num ? 'bg-blue-500' : 'bg-gray-300' }}"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- ШАГ 1: Выбор места --}}
    @if($step === 1)
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-bold mb-6">Выберите заведение</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($places as $place)
                    <button wire:click="selectPlace({{ $place->id }})"
                            class="p-6 border-2 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition text-left">
                        <h3 class="font-bold text-lg">{{ $place->name }}</h3>
                        <p class="text-gray-600 text-sm mt-2">{{ $place->address }}</p>
                    </button>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ШАГ 2: Визуальный выбор стола (ИСПРАВЛЕНО) --}}
    @if($step === 2)
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Выберите стол</h2>
                <button wire:click="goBack" class="text-gray-600 hover:text-gray-900 px-4 py-2 rounded hover:bg-gray-100">
                    ← Назад
                </button>
            </div>

            <div class="mb-4 p-4 bg-blue-50 rounded-lg flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Выбранное заведение:</p>
                    <p class="font-bold text-lg">📍 {{ $placeData['place']['name'] ?? '' }}</p>
                </div>
                @if($resource_id)
                    @php
                        $selectedResource = collect($placeData['resources'] ?? [])->firstWhere('id', $resource_id);
                    @endphp
                    @if($selectedResource)
                        <div class="text-right">
                            <p class="text-sm text-gray-600">Выбранный стол:</p>
                            <p class="font-bold text-lg text-blue-600">🎱 {{ $selectedResource['code'] }}</p>
                        </div>
                    @endif
                @endif
            </div>

            {{-- Визуальная карта зала --}}
            <div class="border-2 border-gray-300 rounded-lg overflow-auto bg-gray-50 mb-6"
                 style="max-height: calc(100vh - 400px);">
                <div class="inline-block min-w-full p-4">
                    @php
                        $gridWidth = $placeData['place']['grid_width'] ?? 20;
                        $gridHeight = $placeData['place']['grid_height'] ?? 10;
                        $zones = $placeData['zones'] ?? [];
                        $resources = $placeData['resources'] ?? [];
                    @endphp

                    <div class="relative grid gap-0" 
                         style="grid-template-columns: repeat({{ $gridWidth }}, minmax(50px, 1fr)); 
                                grid-template-rows: repeat({{ $gridHeight }}, minmax(50px, 1fr));">
                        
                        {{-- Ячейки сетки с зонами --}}
                        @for($y = 0; $y < $gridHeight; $y++)
                            @for($x = 0; $x < $gridWidth; $x++)
                                @php
                                    // ✅ ИСПРАВЛЕНО: Правильное определение зоны ячейки
                                    $cellZone = null;
                                    foreach($zones as $zone) {
                                        $coordinates = is_string($zone['coordinates']) 
                                            ? json_decode($zone['coordinates'], true) 
                                            : ($zone['coordinates'] ?? []);
                                        
                                        if (!empty($coordinates)) {
                                            foreach ($coordinates as $coord) {
                                                if (isset($coord['x']) && isset($coord['y']) && 
                                                    (int)$coord['x'] === $x && (int)$coord['y'] === $y) {
                                                    $cellZone = $zone;
                                                    break 2;
                                                }
                                            }
                                        }
                                    }
                                @endphp
                                
                                <div class="aspect-square border border-gray-200 relative"
                                     style="background-color: {{ $cellZone ? ($cellZone['color'] ?? '#3B82F6') : 'white' }};
                                            opacity: {{ $cellZone ? '0.3' : '1' }};
                                            min-width: 50px;
                                            min-height: 50px;">
                                </div>
                            @endfor
                        @endfor

                        {{-- Столы поверх сетки --}}
                        @foreach($resources as $resource)
                            @php
                                // Вычисляем размеры с учетом поворота
                                $displayWidth = ($resource['rotation'] === 90 || $resource['rotation'] === 270) 
                                    ? $resource['grid_height'] 
                                    : $resource['grid_width'];
                                $displayHeight = ($resource['rotation'] === 90 || $resource['rotation'] === 270) 
                                    ? $resource['grid_width'] 
                                    : $resource['grid_height'];
                                
                                $isSelected = $resource_id === $resource['id'];
                                
                                // Проверяем доступность стола
                                $isAvailable = true;
                                if (isset($resource['state'])) {
                                    $isAvailable = in_array(strtolower($resource['state']), ['available', 'active', 'доступен']);
                                }
                            @endphp
                            
                            <button
                                wire:click="selectResource({{ $resource['id'] }})"
                                @disabled(!$isAvailable)
                                class="absolute flex flex-col items-center justify-center
                                       border-3 rounded-lg transition-all duration-200
                                       {{ $isSelected 
                                          ? 'border-blue-500 bg-blue-200 shadow-xl ring-4 ring-blue-300 z-20 scale-105' 
                                          : ($isAvailable 
                                             ? 'border-green-600 bg-white hover:border-green-500 hover:bg-green-50 hover:shadow-lg z-10 cursor-pointer' 
                                             : 'border-gray-400 bg-gray-200 cursor-not-allowed opacity-60 z-10') }}"
                                style="grid-column: {{ $resource['grid_x'] + 1 }} / span {{ $displayWidth }};
                                       grid-row: {{ $resource['grid_y'] + 1 }} / span {{ $displayHeight }};
                                       border-width: 3px;">
                                <div class="text-center pointer-events-none p-1">
                                    <div class="text-sm font-bold {{ $isSelected ? 'text-blue-900' : ($isAvailable ? 'text-green-900' : 'text-gray-600') }}">
                                        {{ $resource['code'] }}
                                    </div>
                                    <div class="text-xs {{ $isSelected ? 'text-blue-700' : ($isAvailable ? 'text-gray-700' : 'text-gray-500') }} truncate max-w-full">
                                        {{ $resource['model_name'] ?? 'N/A' }}
                                    </div>
                                    @if(!$isAvailable)
                                        <div class="text-xs text-red-600 font-medium mt-1">
                                            Занят
                                        </div>
                                    @endif
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Легенда --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                {{-- Легенда зон --}}
                @if(count($zones) > 0)
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">🗺️ Зоны:</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($zones as $zone)
                                <div class="flex items-center space-x-2 px-3 py-1 rounded-full text-sm"
                                     style="background-color: {{ $zone['color'] ?? '#3B82F6' }}40; border: 2px solid {{ $zone['color'] ?? '#3B82F6' }};">
                                    <span class="font-medium">{{ $zone['name'] }}</span>
                                    <span class="opacity-75">×{{ $zone['price_coef'] ?? 1.0 }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Легенда статусов --}}
                <div class="p-4 bg-gray-50 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">📊 Статусы столов:</h3>
                    <div class="space-y-2">
                        <div class="flex items-center">
                            <div class="w-6 h-6 bg-white border-3 border-green-600 rounded mr-2"></div>
                            <span class="text-sm">Доступен для бронирования</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-6 h-6 bg-blue-100 border-3 border-blue-500 rounded mr-2"></div>
                            <span class="text-sm">Выбранный стол</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-6 h-6 bg-gray-200 border-3 border-gray-400 rounded mr-2 opacity-60"></div>
                            <span class="text-sm">Занят / Недоступен</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Кнопка продолжить --}}
            <div class="flex justify-end">
                <button wire:click="proceedToTimeSelection"
                        @disabled(!$resource_id)
                        class="px-8 py-3 rounded-lg font-medium text-lg transition
                               {{ $resource_id 
                                  ? 'bg-blue-600 hover:bg-blue-700 text-white' 
                                  : 'bg-gray-300 text-gray-500 cursor-not-allowed' }}">
                    Далее: Выбрать время →
                </button>
            </div>
        </div>
    @endif

    {{-- ШАГ 3: Выбор времени --}}
    @if($step === 3)
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Выберите время</h2>
                <button wire:click="goBack" class="text-gray-600 hover:text-gray-900 px-4 py-2 rounded hover:bg-gray-100">
                    ← Назад
                </button>
            </div>

            {{-- Информация о выборе --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="p-4 bg-blue-50 rounded-lg">
                    <p class="text-sm text-gray-600">Заведение</p>
                    <p class="font-bold">📍 {{ $placeData['place']['name'] ?? '' }}</p>
                </div>
                <div class="p-4 bg-green-50 rounded-lg">
                    <p class="text-sm text-gray-600">Стол</p>
                    <p class="font-bold">🎱 {{ collect($placeData['resources'] ?? [])->firstWhere('id', $resource_id)['code'] ?? 'N/A' }}</p>
                </div>
            </div>

            {{-- Выбор даты --}}
            <div class="mb-6">
                <label class="block font-medium mb-3 text-lg">📅 Дата бронирования</label>
                <input type="date" wire:model.live="date" 
                       min="{{ now()->format('Y-m-d') }}"
                       class="border-2 border-gray-300 rounded-lg px-4 py-3 text-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
            </div>

            {{-- Временные слоты (в строчку с прокруткой) --}}
            <div class="mb-6">
                <div class="flex items-center justify-between mb-3">
                    <label class="block font-medium text-lg">⏰ Выберите время</label>
                    @if(count($selectedSlots) > 0)
                        <span class="text-sm text-blue-600 font-medium">
                            Выбрано: {{ count($selectedSlots) }} {{ count($selectedSlots) === 1 ? 'час' : (count($selectedSlots) < 5 ? 'часа' : 'часов') }}
                        </span>
                    @endif
                </div>

                {{-- Слоты в виде горизонтальной ленты --}}
                <div class="relative">
                    <div class="overflow-x-auto pb-4">
                        <div class="flex gap-3 min-w-max">
                            @foreach($availableSlots as $time => $slot)
                                <button wire:click="toggleSlot('{{ $time }}')"
                                        @disabled(!$slot['available'])
                                        class="flex-shrink-0 p-4 rounded-lg border-2 text-center transition-all duration-200
                                               min-w-[120px] hover:scale-105
                                               {{ in_array($time, $selectedSlots) 
                                                  ? 'border-blue-500 bg-blue-500 text-white shadow-lg transform scale-105' 
                                                  : ($slot['available'] 
                                                     ? 'border-gray-300 bg-white hover:border-blue-400 hover:bg-blue-50 hover:shadow-md' 
                                                     : 'border-gray-200 bg-gray-100 opacity-50 cursor-not-allowed') }}">
                                    <div class="font-bold text-xl mb-1">{{ $time }}</div>
                                    @if($slot['available'])
                                        <div class="text-sm {{ in_array($time, $selectedSlots) ? 'text-blue-100' : 'text-gray-600' }}">
                                            {{ number_format($slot['price'], 0) }} ₽
                                        </div>
                                    @else
                                        <div class="text-sm font-medium text-red-600">Занято</div>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>
                    
                    {{-- Градиенты для индикации прокрутки --}}
                    <div class="absolute left-0 top-0 bottom-4 w-8 bg-gradient-to-r from-white to-transparent pointer-events-none"></div>
                    <div class="absolute right-0 top-0 bottom-4 w-8 bg-gradient-to-l from-white to-transparent pointer-events-none"></div>
                </div>

                <p class="text-sm text-gray-500 mt-2">💡 Прокрутите влево/вправо для выбора времени</p>
            </div>

            {{-- Выбранные слоты (компактно) --}}
            @if(count($selectedSlots) > 0)
                <div class="mb-6 p-4 bg-blue-50 rounded-lg border-2 border-blue-200">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <p class="font-medium text-blue-900 mb-2">✓ Выбранное время:</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($selectedSlots as $time)
                                    <span class="inline-flex items-center px-3 py-1 bg-blue-500 text-white rounded-full text-sm font-medium">
                                        {{ $time }}
                                        <button wire:click="toggleSlot('{{ $time }}')" 
                                                class="ml-2 hover:bg-blue-600 rounded-full p-0.5">
                                            ✕
                                        </button>
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        <div class="ml-4 text-right">
                            <p class="text-sm text-gray-600">Стоимость:</p>
                            <p class="text-2xl font-bold text-green-600">
                                {{ number_format($totalAmount, 0) }} ₽
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Быстрый выбор --}}
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <p class="text-sm font-medium text-gray-700 mb-3">⚡ Быстрый выбор:</p>
                <div class="flex flex-wrap gap-2">
                    <button wire:click="quickSelect(1)" 
                            class="px-4 py-2 bg-white border-2 border-gray-300 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition">
                        1 час
                    </button>
                    <button wire:click="quickSelect(2)" 
                            class="px-4 py-2 bg-white border-2 border-gray-300 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition">
                        2 часа
                    </button>
                    <button wire:click="quickSelect(3)" 
                            class="px-4 py-2 bg-white border-2 border-gray-300 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition">
                        3 часа
                    </button>
                    <button wire:click="clearSlots" 
                            class="px-4 py-2 bg-white border-2 border-red-300 text-red-600 rounded-lg hover:bg-red-50 transition">
                        Очистить
                    </button>
                </div>
            </div>

            {{-- Итого и кнопка продолжить --}}
            <div class="flex justify-between items-center pt-6 border-t-2">
                <div>
                    <p class="text-sm text-gray-600">Итого к оплате:</p>
                    <p class="text-3xl font-bold text-green-600">
                        {{ number_format($totalAmount, 0) }} ₽
                    </p>
                </div>
                <button wire:click="proceedToEquipment"
                        @disabled(count($selectedSlots) === 0)
                        class="px-8 py-3 rounded-lg font-medium text-lg transition
                               {{ count($selectedSlots) > 0
                                  ? 'bg-blue-600 hover:bg-blue-700 text-white' 
                                  : 'bg-gray-300 text-gray-500 cursor-not-allowed' }}">
                    Далее: Доп. услуги →
                </button>
            </div>
        </div>
    @endif

    {{-- ШАГ 4: Оборудование (ИСПРАВЛЕНО) --}}
    @if($step === 4)
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Дополнительное оборудование</h2>
                <button wire:click="goBack" class="text-gray-600 hover:text-gray-900 px-4 py-2 rounded hover:bg-gray-100">
                    ← Назад
                </button>
            </div>

            @if(count($availableEquipment) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                    @foreach($availableEquipment as $eq)
                        <div class="border-2 rounded-lg p-4 hover:border-blue-400 hover:shadow-md transition">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h3 class="font-bold text-lg">{{ $eq['name'] }}</h3>
                                    <p class="text-xs text-gray-500">Код: {{ $eq['code'] }}</p>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-bold text-green-600">{{ number_format($eq['price'], 0) }} ₽</div>
                                    <div class="text-xs text-gray-500">за единицу</div>
                                </div>
                            </div>
                            
                            <div class="mb-3 p-2 bg-gray-50 rounded text-center">
                                <span class="text-sm text-gray-700">
                                    Доступно: <strong class="text-blue-600">{{ $eq['available_qty'] }}</strong> из {{ $eq['total_qty'] }}
                                </span>
                            </div>
                            
                            <button wire:click="addEquipment({{ $eq['resource_id'] }})"
                                    @disabled($eq['available_qty'] < 1)
                                    class="w-full py-2 rounded transition
                                           {{ $eq['available_qty'] > 0
                                              ? 'bg-blue-500 hover:bg-blue-600 text-white'
                                              : 'bg-gray-200 text-gray-500 cursor-not-allowed' }}">
                                {{ $eq['available_qty'] > 0 ? '+ Добавить' : 'Недоступно' }}
                            </button>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 bg-gray-50 rounded-lg">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">Нет доступного оборудования на выбранное время</p>
                </div>
            @endif

            {{-- Выбранное оборудование --}}
            @if(count($equipment) > 0)
                <div class="mb-6">
                    <h3 class="font-medium text-lg mb-3">📦 Добавлено в корзину:</h3>
                    <div class="space-y-3">
                        @foreach($equipment as $index => $item)
                            <div class="flex items-center justify-between bg-blue-50 border-2 border-blue-200 p-4 rounded-lg">
                                <div class="flex-1">
                                    <p class="font-bold">{{ $item['name'] }}</p>
                                    <p class="text-sm text-gray-600">
                                        {{ number_format($item['price'], 0) }} ₽ × {{ $item['qty'] }} = 
                                        <span class="font-semibold text-green-600">
                                            {{ number_format(($item['price'] * $item['qty']), 0) }} ₽
                                        </span>
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">Максимум доступно: {{ $item['max_qty'] }}</p>
                                </div>
                                <div class="flex items-center gap-3 ml-4">
                                    <div class="flex items-center border-2 border-gray-300 rounded-lg overflow-hidden">
                                        <button wire:click="updateEquipmentQty({{ $index }}, {{ max(1, $item['qty'] - 1) }})"
                                                class="px-3 py-2 bg-gray-100 hover:bg-gray-200 transition">
                                            −
                                        </button>
                                        <input type="number" 
                                               value="{{ $item['qty'] }}"
                                               wire:change="updateEquipmentQty({{ $index }}, $event.target.value)"
                                               min="1" 
                                               max="{{ $item['max_qty'] }}"
                                               class="w-16 border-0 text-center font-bold py-2">
                                        <button wire:click="updateEquipmentQty({{ $index }}, {{ min($item['max_qty'], $item['qty'] + 1) }})"
                                                @disabled($item['qty'] >= $item['max_qty'])
                                                class="px-3 py-2 bg-gray-100 hover:bg-gray-200 transition
                                                       {{ $item['qty'] >= $item['max_qty'] ? 'opacity-50 cursor-not-allowed' : '' }}">
                                            +
                                        </button>
                                    </div>
                                    <button wire:click="removeEquipment({{ $index }})"
                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Итого и кнопки --}}
            <div class="flex justify-between items-center pt-6 border-t-2">
                <button wire:click="skipEquipment" 
                        class="text-gray-600 hover:text-gray-900 px-4 py-2 rounded hover:bg-gray-100">
                    Пропустить
                </button>
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <p class="text-sm text-gray-600">Итого:</p>
                        <p class="text-2xl font-bold text-green-600">
                            {{ number_format($totalAmount, 0) }} ₽
                        </p>
                    </div>
                    <button wire:click="proceedToClientData" 
                            class="bg-blue-500 hover:bg-blue-600 text-white px-8 py-3 rounded-lg font-medium text-lg transition">
                        Далее →
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ШАГ 5: Данные клиента --}}
    @if($step === 5)
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Ваши данные</h2>
                <button wire:click="goBack" class="text-gray-600 hover:text-gray-900 px-4 py-2 rounded hover:bg-gray-100">
                    ← Назад
                </button>
            </div>

            @guest
                <div class="space-y-4 mb-6">
                    <div>
                        <label class="block font-medium mb-2">Имя *</label>
                        <input type="text" wire:model="guest_name" 
                               class="w-full border-2 rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                        @error('guest_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-medium mb-2">Email *</label>
                        <input type="email" wire:model="guest_email" 
                               class="w-full border-2 rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                        @error('guest_email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-medium mb-2">Телефон</label>
                        <input type="tel" wire:model="guest_phone" 
                               class="w-full border-2 rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                    </div>
                </div>
            @else
                <div class="p-4 bg-green-50 rounded-lg mb-6">
                    <p>Бронирование на имя: <strong>{{ auth()->user()->name }}</strong></p>
                    <p class="text-sm text-gray-600">{{ auth()->user()->email }}</p>
                </div>
            @endguest

            <div class="mb-6">
                <label class="block font-medium mb-2">Комментарий</label>
                <textarea wire:model="comment" rows="3" 
                          class="w-full border-2 rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                          placeholder="Укажите особые пожелания или дополнительную информацию"></textarea>
            </div>

            {{-- Итого --}}
            <div class="p-6 bg-gradient-to-br from-green-50 to-blue-50 rounded-lg mb-6 border-2 border-green-200">
                <div class="flex justify-between items-center">
                    <span class="text-lg font-medium">ИТОГО к оплате:</span>
                    <span class="text-4xl font-bold text-green-600">{{ number_format($totalAmount, 0) }} ₽</span>
                </div>
            </div>

            <button wire:click="createPendingBooking" 
                    class="w-full bg-blue-500 hover:bg-blue-600 text-white px-6 py-4 rounded-lg font-medium text-lg transition shadow-lg hover:shadow-xl">
                Перейти к оплате →
            </button>
        </div>
    @endif

    {{-- ШАГ 6: Оплата --}}
    @if($step === 6 && $booking)
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-bold mb-6">Оплата</h2>

            <div class="mb-6 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded">
                <p class="text-sm">⏱ Бронирование действительно в течение <strong>30 минут</strong></p>
                <p class="text-sm text-gray-600">Истекает: {{ $booking->expires_at->format('d.m.Y H:i') }}</p>
            </div>

            <div class="space-y-3 mb-6">
                <button wire:click="payBooking('card')"
                        class="w-full p-4 border-2 rounded-lg hover:border-blue-500 hover:bg-blue-50 text-left flex items-center transition">
                    <span class="text-2xl mr-3">💳</span>
                    <div>
                        <div class="font-medium">Оплатить картой</div>
                        <div class="text-sm text-gray-500">Visa, MasterCard, Мир</div>
                    </div>
                </button>

                <button wire:click="payBooking('online')"
                        class="w-full p-4 border-2 rounded-lg hover:border-blue-500 hover:bg-blue-50 text-left flex items-center transition">
                    <span class="text-2xl mr-3">🌐</span>
                    <div>
                        <div class="font-medium">Оплатить онлайн</div>
                        <div class="text-sm text-gray-500">СБП, Онлайн-банк</div>
                    </div>
                </button>
            </div>

            <button wire:click="skipPayment" 
                    class="w-full text-center text-gray-600 hover:text-gray-900 py-2 hover:bg-gray-100 rounded transition">
                Оплатить позже
            </button>
        </div>
    @endif

    {{-- ШАГ 7: Успех --}}
    @if($step === 7 && $booking)
        <div class="bg-white rounded-lg shadow-lg p-6 text-center">
            <div class="mb-6">
                <div class="text-6xl mb-4">✅</div>
                <h2 class="text-3xl font-bold text-green-600 mb-2">
                    @if($booking->isPaid())
                        Оплата прошла успешно!
                    @else
                        Бронирование создано!
                    @endif
                </h2>
                <p class="text-gray-600">
                    @if($booking->isPaid())
                        Ваше бронирование подтверждено
                    @else
                        Оплатите в течение 30 минут
                    @endif
                </p>
            </div>

            <div class="bg-gray-50 rounded-lg p-6 mb-6 text-left">
                <h3 class="font-bold mb-4 text-lg">Детали бронирования:</h3>
                
                <div class="space-y-3">
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Номер бронирования:</span>
                        <span class="font-bold">#{{ $booking->id }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Место:</span>
                        <span class="font-medium">{{ $booking->place->name }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Стол:</span>
                        <span class="font-medium">{{ $booking->resource->code }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Дата:</span>
                        <span class="font-medium">{{ $booking->slots->first()->slot_date ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Время:</span>
                        <span class="font-medium">
                            @foreach($booking->slots as $slot)
                                {{ $slot->slot_time }}@if(!$loop->last), @endif
                            @endforeach
                        </span>
                    </div>
                    <div class="flex justify-between pt-3 border-t-2">
                        <span class="text-gray-800 font-bold text-lg">Итого:</span>
                        <span class="font-bold text-2xl text-green-600">{{ $booking->getTotalAmountFormatted() }}</span>
                    </div>
                    @if($booking->isPaid())
                    <div class="flex justify-between py-2">
                        <span class="text-gray-600">Оплачено:</span>
                        <span class="text-green-600 font-medium">✓ {{ $booking->payment_method }}</span>
                    </div>
                    @endif
                </div>
            </div>

            @if(!$booking->isPaid())
            <div class="mb-6 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded">
                <p class="text-sm">⚠️ Не забудьте оплатить в течение <strong>30 минут</strong></p>
                <p class="text-sm text-gray-600">Иначе бронирование будет автоматически отменено</p>
            </div>
            @endif

            <div class="space-y-3">
                <a href="/" class="block w-full bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium transition">
                    На главную
                </a>
                
                @if(!$booking->isPaid())
                <button wire:click="$set('step', 6)" 
                        class="block w-full bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-medium transition">
                    Оплатить сейчас
                </button>
                @endif
            </div>
        </div>
    @endif
</div>

@push('styles')
<style>
    /* Кастомная прокрутка для слотов времени */
    .overflow-x-auto::-webkit-scrollbar {
        height: 8px;
    }
    
    .overflow-x-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: #3B82F6;
        border-radius: 10px;
    }
    
    .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: #2563EB;
    }
    
    /* Стили для borders */
    .border-3 {
        border-width: 3px;
    }
</style>
@endpush