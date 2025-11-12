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

    {{-- ШАГ 2: Визуальный выбор стола --}}
    @if($step === 2)
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Выберите стол</h2>
                <button wire:click="goBack" class="text-gray-600 hover:text-gray-900">← Назад</button>
            </div>

            <div class="mb-4 p-4 bg-blue-50 rounded">
                <p class="text-sm">📍 <strong>{{ $placeData['place']['name'] ?? '' }}</strong></p>
            </div>

            {{-- Визуальная карта зала (сетка) --}}
            <div class="relative border-2 border-gray-300 rounded-lg bg-gray-50 overflow-auto">
                
                @php
                    $gridWidth = $placeData['place']['grid_width'] ?? 20;
                    $gridHeight = $placeData['place']['grid_height'] ?? 10;
                    $cellSize = 60; // пикселей на клетку
                @endphp

                <div class="relative" 
                     style="width: {{ $gridWidth * $cellSize }}px; 
                            height: {{ $gridHeight * $cellSize }}px;">
                
                    {{-- Фоновая картинка (если есть) --}}
                    @if(!empty($placeData['place']['hall_image']))
                        <img src="{{ asset($placeData['place']['hall_image']) }}" 
                             class="absolute inset-0 w-full h-full object-cover opacity-20">
                    @endif

                    {{-- Сетка --}}
                    <svg class="absolute inset-0 pointer-events-none" 
                         width="100%" height="100%">
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

                    {{-- Столы на сетке --}}
                    @foreach($placeData['resources'] ?? [] as $resource)
                        @php
                            $x = $resource['grid_x'] * $cellSize;
                            $y = $resource['grid_y'] * $cellSize;
                            $width = $resource['grid_width'] * $cellSize;
                            $height = $resource['grid_height'] * $cellSize;
                            $rotation = $resource['rotation'];
                        @endphp
                        
                        <button wire:click="selectResource({{ $resource['id'] }})"
                                class="absolute cursor-pointer transition-all hover:scale-105 hover:shadow-lg rounded-lg
                                       flex items-center justify-center font-bold text-white
                                       {{ $resource_id === $resource['id'] ? 'ring-4 ring-blue-500 bg-blue-500' : 'bg-green-500 hover:bg-green-600' }}"
                                style="left: {{ $x }}px; 
                                       top: {{ $y }}px; 
                                       width: {{ $width }}px; 
                                       height: {{ $height }}px;
                                       transform: rotate({{ $rotation }}deg);
                                       transform-origin: center;">
                            {{ $resource['code'] }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="mt-4 flex gap-4">
                <div class="flex items-center">
                    <div class="w-6 h-6 bg-green-500 rounded mr-2"></div>
                    <span class="text-sm">Доступен</span>
                </div>
                <div class="flex items-center">
                    <div class="w-6 h-6 bg-blue-500 rounded mr-2"></div>
                    <span class="text-sm">Выбран</span>
                </div>
            </div>
        </div>
    @endif

    {{-- ШАГ 3: Выбор времени (слоты) --}}
    @if($step === 3)
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Выберите время</h2>
                <button wire:click="goBack" class="text-gray-600 hover:text-gray-900">← Назад</button>
            </div>

            {{-- Выбор даты --}}
            <div class="mb-6">
                <label class="block font-medium mb-2">Дата бронирования</label>
                <input type="date" wire:model.live="date" 
                       min="{{ now()->format('Y-m-d') }}"
                       class="border rounded px-3 py-2">
            </div>

            {{-- Слоты (карточки) --}}
            <div class="grid grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-3">
                @foreach($availableSlots as $time => $slot)
                    <button wire:click="toggleSlot('{{ $time }}')"
                            @disabled(!$slot['available'])
                            class="p-4 rounded-lg border-2 text-center transition
                                   {{ in_array($time, $selectedSlots) 
                                      ? 'border-blue-500 bg-blue-500 text-white' 
                                      : ($slot['available'] 
                                         ? 'border-gray-300 hover:border-blue-400 hover:bg-blue-50' 
                                         : 'border-gray-200 bg-gray-100 opacity-50 cursor-not-allowed') }}">
                        <div class="font-bold">{{ $time }}</div>
                        @if($slot['available'])
                            <div class="text-xs mt-1">{{ number_format($slot['price'] / 100, 0) }} ₽</div>
                        @else
                            <div class="text-xs mt-1 text-red-600">Занято</div>
                        @endif
                    </button>
                @endforeach
            </div>

            {{-- Выбранное время --}}
            @if(count($selectedSlots) > 0)
                <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                    <p class="font-medium mb-2">Выбрано слотов: {{ count($selectedSlots) }}</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($selectedSlots as $time)
                            <span class="px-3 py-1 bg-blue-500 text-white rounded-full text-sm">{{ $time }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="mt-6 flex justify-between items-center">
                <div class="text-2xl font-bold text-green-600">
                    {{ number_format($totalAmount / 100, 2) }} ₽
                </div>
                <button wire:click="proceedToEquipment" 
                        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium">
                    Далее →
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