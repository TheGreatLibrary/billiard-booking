<div class="max-w-7xl mx-auto p-6">
    {{-- Flash сообщения --}}
    @if (session()->has('error'))
        <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            <strong>Ошибка:</strong> {{ session('error') }}
        </div>
    @endif

    @if (session()->has('success'))
        <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
            <strong>Успех:</strong> {{ session('success') }}
        </div>
    @endif

    @if (session()->has('info'))
        <div class="mb-6 p-4 bg-blue-100 border border-blue-400 text-blue-700 rounded-lg">
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

    {{-- ШАГ 2: Визуальный выбор стола (УЛУЧШЕННАЯ ВЕРСИЯ) --}}
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
                         style="grid-template-columns: repeat({{ $gridWidth }}, 1fr); 
                                grid-template-rows: repeat({{ $gridHeight }}, 1fr);">
                        
                        {{-- Ячейки сетки с зонами --}}
                        @for($y = 0; $y < $gridHeight; $y++)
                            @for($x = 0; $x < $gridWidth; $x++)
                                @php
                                    // Определяем зону ячейки
                                    $cellZone = null;
                                    foreach($zones as $zone) {
                                        $coordinates = is_string($zone['coordinates']) 
                                            ? json_decode($zone['coordinates'], true) 
                                            : ($zone['coordinates'] ?? []);
                                        
                                        if(in_array(['x' => $x, 'y' => $y], $coordinates)) {
                                            $cellZone = $zone;
                                            break;
                                        }
                                    }
                                @endphp
                                
                                <div class="aspect-square border border-gray-200 relative"
                                     style="background-color: {{ $cellZone ? ($cellZone['color'] ?? '#3B82F6') : 'white' }};
                                            opacity: {{ $cellZone ? '0.2' : '1' }};
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
                                
                                // Проверяем доступность стола (если ключ state существует)
                                $isAvailable = true; // По умолчанию доступен
                                if (isset($resource['state'])) {
                                    $isAvailable = in_array(strtolower($resource['state']), ['available', 'доступен']);
                                }
                            @endphp
                            
                          <button
                                wire:click="selectResource({{ $resource['id'] }})"
                                @disabled(!$isAvailable)
                                class="bg-green-600 absolute flex flex-col items-center justify-center
                                       border-2 rounded-lg transition-all duration-200
                                       {{ $isSelected 
                                          ? 'border-blue-500 bg-blue-200 shadow-xl ring-4 ring-blue-200 z-20 scale-105' 
                                          : ($isAvailable 
                                             ? 'border-green-600 bg-gray-300 hover:border-green-500 hover:bg-green-50 hover:shadow-lg z-10 cursor-pointer' 
                                             : 'border-gray-400 bg-gray-200 cursor-not-allowed z-10') }}"
                                style="grid-column: {{ $resource['grid_x'] + 1 }} / span {{ $displayWidth +50 }};
                                       grid-row: {{ $resource['grid_y'] + 1 }} / span {{ $displayHeight +50}};
                                       transform: rotate({{ $resource['rotation'] }}deg);
                                       transform-origin: center center;">
                                <div class="text-center pointer-events-none">
                                    <div class="text-base font-bold {{ $isSelected ? 'text-blue-900' : ($isAvailable ? 'text-green-900' : 'text-gray-600') }}">
                                        {{ $resource['code'] }}
                                    </div>
                                    <div class="text-xs {{ $isSelected ? 'text-blue-700' : ($isAvailable ? 'text-white' : 'text-gray-500') }} truncate max-w-full px-1">
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
                            <div class="w-6 h-6 bg-green-50 border-2 border-green-600 rounded mr-2"></div>
                            <span class="text-sm">Доступен для бронирования</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-6 h-6 bg-blue-100 border-2 border-blue-500 rounded mr-2"></div>
                            <span class="text-sm">Выбранный стол</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-6 h-6 bg-gray-200 border-2 border-gray-400 rounded mr-2 opacity-50"></div>
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

    {{-- ШАГ 3: Выбор времени (УЛУЧШЕННАЯ ВЕРСИЯ - В СТРОЧКУ) --}}
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
                                            {{ number_format($slot['price'] / 100, 0) }} ₽
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
                                {{ number_format($totalAmount / 100, 0) }} ₽
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
                        {{ number_format($totalAmount / 100, 0) }} ₽
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

    {{-- ШАГ 4: Оборудование --}}
    @if($step === 4)
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Дополнительное оборудование</h2>
                <button wire:click="goBack" class="text-gray-600 hover:text-gray-900">← Назад</button>
            </div>

            @if(count($availableEquipment) > 0)
                <div class="grid grid-cols-3 gap-4 mb-6">
                    @foreach($availableEquipment as $eq)
                        <div class="border rounded-lg p-4">
                            <h3 class="font-medium">{{ $eq['name'] }}</h3>
                            <p class="text-sm text-gray-500 mb-3">{{ number_format($eq['price'] / 100, 0) }} ₽</p>
                            <button wire:click="addEquipment({{ $eq['id'] }})"
                                    class="w-full bg-blue-500 text-white px-3 py-2 rounded text-sm">
                                + Добавить
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Выбранное оборудование --}}
            @if(count($equipment) > 0)
                <div class="mb-6">
                    <h3 class="font-medium mb-3">Добавлено:</h3>
                    @foreach($equipment as $index => $item)
                        <div class="flex items-center justify-between bg-gray-50 p-3 rounded mb-2">
                            <div>
                                <p class="font-medium">{{ $item['name'] }}</p>
                                <p class="text-sm text-gray-500">{{ number_format($item['price'] / 100, 0) }} ₽</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <input type="number" 
                                       value="{{ $item['qty'] }}"
                                       wire:change="updateEquipmentQty({{ $index }}, $event.target.value)"
                                       min="1" 
                                       class="w-16 border rounded px-2 py-1 text-center">
                                <button wire:click="removeEquipment({{ $index }})"
                                        class="text-red-600 hover:text-red-800">✕</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="flex justify-between">
                <button wire:click="skipEquipment" 
                        class="text-gray-600 hover:text-gray-900">Пропустить</button>
                <button wire:click="proceedToClientData" 
                        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg">
                    Далее →
                </button>
            </div>
        </div>
    @endif

    {{-- ШАГ 5: Данные клиента --}}
    @if($step === 5)
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Ваши данные</h2>
                <button wire:click="goBack" class="text-gray-600 hover:text-gray-900">← Назад</button>
            </div>

            @guest
                <div class="space-y-4 mb-6">
                    <div>
                        <label class="block font-medium mb-2">Имя *</label>
                        <input type="text" wire:model="guest_name" 
                               class="w-full border rounded px-3 py-2">
                        @error('guest_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-medium mb-2">Email *</label>
                        <input type="email" wire:model="guest_email" 
                               class="w-full border rounded px-3 py-2">
                        @error('guest_email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-medium mb-2">Телефон</label>
                        <input type="tel" wire:model="guest_phone" 
                               class="w-full border rounded px-3 py-2">
                    </div>
                </div>
            @else
                <div class="p-4 bg-green-50 rounded mb-6">
                    <p>Бронирование на имя: <strong>{{ auth()->user()->name }}</strong></p>
                    <p class="text-sm text-gray-600">{{ auth()->user()->email }}</p>
                </div>
            @endguest

            <div class="mb-6">
                <label class="block font-medium mb-2">Комментарий</label>
                <textarea wire:model="comment" rows="3" 
                          class="w-full border rounded px-3 py-2"></textarea>
            </div>

            {{-- Итого --}}
            <div class="p-4 bg-gray-50 rounded mb-6">
                <div class="flex justify-between items-center">
                    <span class="text-lg font-medium">ИТОГО к оплате:</span>
                    <span class="text-3xl font-bold text-green-600">{{ number_format($totalAmount / 100, 2) }} ₽</span>
                </div>
            </div>

            <button wire:click="createPendingBooking" 
                    class="w-full bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium text-lg">
                Перейти к оплате →
            </button>
        </div>
    @endif

    {{-- ШАГ 6: Оплата --}}
    @if($step === 6 && $booking)
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-bold mb-6">Оплата</h2>

            <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded">
                <p class="text-sm">⏱ Бронирование действительно в течение <strong>30 минут</strong></p>
                <p class="text-sm text-gray-600">Истекает: {{ $booking->expires_at->format('d.m.Y H:i') }}</p>
            </div>

            <div class="space-y-3 mb-6">
                <button wire:click="payBooking('card')"
                        class="w-full p-4 border-2 rounded-lg hover:border-blue-500 hover:bg-blue-50 text-left">
                    💳 Оплатить картой
                </button>

                <button wire:click="payBooking('online')"
                        class="w-full p-4 border-2 rounded-lg hover:border-blue-500 hover:bg-blue-50 text-left">
                    🌐 Оплатить онлайн (СБП)
                </button>
            </div>

            <button wire:click="skipPayment" 
                    class="w-full text-center text-gray-600 hover:text-gray-900">
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
                <h3 class="font-bold mb-4">Детали бронирования:</h3>
                
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Номер бронирования:</span>
                        <span class="font-bold">#{{ $booking->id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Место:</span>
                        <span class="font-medium">{{ $booking->place->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Стол:</span>
                        <span class="font-medium">{{ $booking->resource->code }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Дата:</span>
                        <span class="font-medium">{{ $booking->slots->first()->slot_date ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Время:</span>
                        <span class="font-medium">
                            @foreach($booking->slots as $slot)
                                {{ $slot->slot_time }}@if(!$loop->last), @endif
                            @endforeach
                        </span>
                    </div>
                    <div class="flex justify-between border-t pt-2">
                        <span class="text-gray-600 font-bold">Итого:</span>
                        <span class="font-bold text-lg text-green-600">{{ $booking->getTotalAmountFormatted() }}</span>
                    </div>
                    @if($booking->isPaid())
                    <div class="flex justify-between">
                        <span class="text-gray-600">Оплачено:</span>
                        <span class="text-green-600 font-medium">✓ {{ $booking->payment_method }}</span>
                    </div>
                    @endif
                </div>
            </div>

            @if(!$booking->isPaid())
            <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded">
                <p class="text-sm">⚠️ Не забудьте оплатить в течение <strong>30 минут</strong></p>
                <p class="text-sm text-gray-600">Иначе бронирование будет автоматически отменено</p>
            </div>
            @endif

            <div class="space-y-3">
                <a href="/" class="block w-full bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium">
                    На главную
                </a>
                
                @if(!$booking->isPaid())
                <button wire:click="$set('step', 6)" 
                        class="block w-full bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-medium">
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
</style>
@endpush