<!-- resources/views/bookings/stepper.blade.php -->
<div class="max-w-7xl mx-auto p-6">
    {{-- Flash сообщения --}}
    @if (session()->has('error'))
        <div class="mb-6 p-4 bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-200 rounded-lg shadow-sm dark:shadow-red-900/20">
            <strong class="font-semibold">Ошибка:</strong> {{ session('error') }}
        </div>
    @endif

    @if (session()->has('success'))
        <div class="mb-6 p-4 bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-200 rounded-lg shadow-sm dark:shadow-green-900/20">
            <strong class="font-semibold">Успех:</strong> {{ session('success') }}
        </div>
    @endif

@if (session()->has('info'))
    <div class="mb-6 p-6 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-950/40 dark:to-indigo-950/30 rounded-xl border-2 border-blue-200 dark:border-blue-800">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/40 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="flex-1">
                <p class="font-semibold text-blue-800 dark:text-blue-200">{{ session('info') }}</p>
            </div>
        </div>
    </div>
@endif

    {{-- Прогресс шагов --}}
    <div class="mb-8">
        <div class="flex justify-between items-center">
            @foreach([1 => 'Место', 2 => 'Стол', 3 => 'Время', 4 => 'Доп. услуги', 5 => 'Данные', 6 => 'Оплата', 7 => 'Готово'] as $num => $name)
                <div class="flex items-center {{ $num < 7 ? 'flex-1' : '' }}">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold transition-all duration-300
                        {{ $step >= $num ? 'bg-blue-500 dark:bg-blue-600 text-white shadow-lg dark:shadow-blue-700/50' : 'bg-gray-300 dark:bg-gray-700 text-gray-600 dark:text-gray-300 shadow dark:shadow-gray-800' }}">
                        @if($num === 7 && $step === 7)
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        @else
                            {{ $num }}
                        @endif
                    </div>
                    <span class="ml-2 text-sm font-medium transition-colors {{ $step >= $num ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">
                        {{ $name }}
                    </span>
                    @if($num < 7)
                        <div class="flex-1 h-1 mx-2 rounded-full transition-colors {{ $step > $num ? 'bg-blue-500 dark:bg-blue-600' : 'bg-gray-300 dark:bg-gray-700' }}"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- ШАГ 1: Выбор места --}}
    @if($step === 1)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg dark:shadow-gray-900/30 p-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Выберите заведение</h2>
            <p class="text-gray-600 dark:text-gray-400 mb-6">Выберите подходящее заведение для бронирования</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($places as $place)
                    <button wire:click="selectPlace({{ $place->id }})"
                            class="p-6 border-2 border-gray-300 dark:border-gray-600 rounded-xl hover:border-blue-500 dark:hover:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-300 text-left bg-white dark:bg-gray-700 group shadow-sm hover:shadow-lg dark:hover:shadow-blue-900/20">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/40 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-2">{{ $place->name }}</h3>
                        <p class="text-gray-600 dark:text-gray-300 text-sm">{{ $place->address }}</p>
                    </button>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ШАГ 2: Визуальный выбор стола (УЛУЧШЕННАЯ ВЕРСИЯ) --}}
    @if($step === 2)
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg dark:shadow-xl dark:shadow-gray-900/30 p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Выберите стол</h2>
                    <p class="text-gray-600 dark:text-gray-400">Выберите подходящий стол в выбранном заведении</p>
                </div>
                <button wire:click="goBack" 
                        class="flex items-center gap-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-4 py-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors border border-gray-300 dark:border-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Назад
                </button>
            </div>

            {{-- Информационная панель --}}
            <div class="mb-6 p-6 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-950/40 dark:to-indigo-950/30 rounded-xl border border-blue-200 dark:border-blue-800 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/40 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-1">Выбранное заведение</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white">📍 {{ $placeData['place']['name'] ?? '' }}</p>
                    </div>
                </div>
                
                @if($resource_id)
                    @php
                        $selectedResource = collect($placeData['resources'] ?? [])->firstWhere('id', $resource_id);
                    @endphp
                    @if($selectedResource)
                        <div class="text-right">
                            <p class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-1">Выбранный стол</p>
                            <div class="flex items-center gap-2 justify-end">
                                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/40 rounded-lg flex items-center justify-center">
                                    <span class="text-lg">🎱</span>
                                </div>
                                <p class="text-xl font-bold text-green-600 dark:text-green-400">{{ $selectedResource['code'] }}</p>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="text-right">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Стол не выбран</p>
                        <p class="text-lg text-gray-500 dark:text-gray-500">Выберите стол из плана ниже</p>
                    </div>
                @endif
            </div>

            {{-- Визуальная карта зала --}}
            <div class="border-2 border-gray-300 dark:border-gray-700 rounded-xl overflow-auto bg-gray-50 dark:bg-gray-800 mb-8 shadow-inner"
                 style="max-height: calc(100vh - 400px);">
                <div class="inline-block min-w-full p-6">
                    @php
                        $gridWidth = $placeData['place']['grid_width'] ?? 20;
                        $gridHeight = $placeData['place']['grid_height'] ?? 10;
                        $zones = $placeData['zones'] ?? [];
                        $resources = $placeData['resources'] ?? [];
                    @endphp

                    <div class="relative grid gap-0 bg-white dark:bg-gray-900 rounded-lg p-4 shadow-sm" 
                         style="grid-template-columns: repeat({{ $gridWidth }}, 1fr); 
                                grid-template-rows: repeat({{ $gridHeight }}, 1fr);">
                        
                        {{-- Ячейки сетки с зонами --}}
                        @for($y = 0; $y < $gridHeight; $y++)
                            @for($x = 0; $x < $gridWidth; $x++)
                                @php
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
                                
                                <div class="aspect-square border border-gray-200 dark:border-gray-700 relative transition-colors"
                                     style="background-color: {{ $cellZone ? ($cellZone['color'] ?? '#3B82F6') : 'transparent' }};
                                            opacity: {{ $cellZone ? '0.15' : '1' }};
                                            min-width: 50px;
                                            min-height: 50px;">
                                    @if($cellZone)
                                        <div class="absolute inset-0 border-2 border-dashed" 
                                             style="border-color: {{ $cellZone['color'] ?? '#3B82F6' }}; opacity: 0.3;"></div>
                                    @endif
                                </div>
                            @endfor
                        @endfor

                        {{-- Столы поверх сетки --}}
                        @foreach($resources as $resource)
                            @php
                                $displayWidth = ($resource['rotation'] === 90 || $resource['rotation'] === 270) 
                                    ? $resource['grid_height'] 
                                    : $resource['grid_width'];
                                $displayHeight = ($resource['rotation'] === 90 || $resource['rotation'] === 270) 
                                    ? $resource['grid_width'] 
                                    : $resource['grid_height'];
                                
                                $isSelected = $resource_id === $resource['id'];
                                $isAvailable = true;
                                if (isset($resource['state'])) {
                                    $isAvailable = in_array(strtolower($resource['state']), ['available', 'доступен']);
                                }
                            @endphp
                            
                            <button
                                wire:click="selectResource({{ $resource['id'] }})"
                                @disabled(!$isAvailable)
                                class="absolute flex flex-col items-center justify-center
                                       border-2 rounded-xl transition-all duration-300 group
                                       {{ $isSelected 
                                          ? 'border-blue-500 dark:border-blue-400 bg-blue-100 dark:bg-blue-900/40 shadow-2xl ring-4 ring-blue-200 dark:ring-blue-800 z-20 scale-110' 
                                          : ($isAvailable 
                                             ? 'border-green-500 dark:border-green-400 bg-white dark:bg-gray-700 hover:border-green-600 dark:hover:border-green-300 hover:bg-green-50 dark:hover:bg-green-900/20 hover:shadow-xl z-10 cursor-pointer' 
                                             : 'border-gray-400 dark:border-gray-600 bg-gray-200 dark:bg-gray-800 cursor-not-allowed z-10 opacity-60') }}"
                                style="grid-column: {{ $resource['grid_x'] + 1 }} / span {{ $displayWidth + 50 }};
                                       grid-row: {{ $resource['grid_y'] + 1 }} / span {{ $displayHeight + 50 }};
                                       transform: rotate({{ $resource['rotation'] }}deg);
                                       transform-origin: center center;
                                       min-width: {{ max(80, $displayWidth * 25) }}px;
                                       min-height: {{ max(80, $displayHeight * 25) }}px;">
                                <div class="text-center pointer-events-none transform group-hover:scale-105 transition-transform">
                                    <div class="flex items-center justify-center gap-1 mb-1">
                                        <div class="text-base font-bold {{ $isSelected ? 'text-blue-900 dark:text-blue-100' : ($isAvailable ? 'text-green-900 dark:text-green-100' : 'text-gray-600 dark:text-gray-400') }}">
                                            {{ $resource['code'] }}
                                        </div>
                                        @if($isSelected)
                                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="text-xs {{ $isSelected ? 'text-blue-700 dark:text-blue-300' : ($isAvailable ? 'text-gray-600 dark:text-gray-300' : 'text-gray-500 dark:text-gray-500') }} truncate max-w-full px-1">
                                        {{ $resource['model_name'] ?? 'Стол' }}
                                    </div>
                                    @if(!$isAvailable)
                                        <div class="text-xs text-red-600 dark:text-red-400 font-medium mt-1 bg-red-100 dark:bg-red-900/30 px-2 py-1 rounded">
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
                                <div class="flex items-center justify-between p-3 rounded-lg border-2 transition-colors hover:bg-white dark:hover:bg-gray-700"
                                     style="border-color: {{ $zone['color'] ?? '#3B82F6' }}; background-color: {{ $zone['color'] ?? '#3B82F6' }}10;">
                                    <div class="flex items-center gap-3">
                                        <div class="w-4 h-4 rounded-full" style="background-color: {{ $zone['color'] ?? '#3B82F6' }};"></div>
                                        <span class="font-medium text-gray-900 dark:text-white">{{ $zone['name'] }}</span>
                                    </div>
                                    <span class="text-sm font-semibold px-2 py-1 rounded-full bg-white dark:bg-gray-600 text-gray-700 dark:text-gray-300">
                                        ×{{ $zone['price_coef'] ?? 1.0 }}
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

            {{-- Кнопка продолжить --}}
            <div class="flex justify-end">
                <button wire:click="proceedToTimeSelection"
                        @disabled(!$resource_id)
                        class="px-8 py-4 rounded-xl font-semibold text-lg transition-all duration-300 flex items-center gap-2
                               {{ $resource_id 
                                  ? 'bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-600 text-white shadow-lg hover:shadow-xl hover:scale-105' 
                                  : 'bg-gray-300 dark:bg-gray-700 text-gray-500 dark:text-gray-400 cursor-not-allowed shadow-sm' }}">
                    <span>Далее: Выбрать время</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>
        </div>
    @endif

    {{-- Остальные шаги остаются с аналогичными улучшениями для темной темы --}}

     {{-- ШАГ 3: Выбор времени (УЛУЧШЕННАЯ ВЕРСИЯ) --}}
    @if($step === 3)
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg dark:shadow-gray-900/30 p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Выберите время</h2>
                    <p class="text-gray-600 dark:text-gray-400">Выберите дату и время для бронирования</p>
                </div>
                <button wire:click="goBack" 
                        class="flex items-center gap-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-4 py-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors border border-gray-300 dark:border-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Назад
                </button>
            </div>

            {{-- Информация о выборе --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="p-6 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-950/40 dark:to-indigo-950/30 rounded-xl border border-blue-200 dark:border-blue-800">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/40 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-blue-800 dark:text-blue-200">Заведение</p>
                            <p class="font-bold text-lg dark:text-white">📍 {{ $placeData['place']['name'] ?? '' }}</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-950/40 dark:to-emerald-950/30 rounded-xl border border-green-200 dark:border-green-800">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-green-100 dark:bg-green-900/40 rounded-lg flex items-center justify-center">
                            <span class="text-lg">🎱</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-green-800 dark:text-green-200">Стол</p>
                            <p class="font-bold text-lg dark:text-white">{{ collect($placeData['resources'] ?? [])->firstWhere('id', $resource_id)['code'] ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Выбор даты --}}
            <div class="mb-8">
                <label class="block font-semibold mb-4 text-lg dark:text-white flex items-center gap-2">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Дата бронирования
                </label>
                <input type="date" wire:model.live="date" 
                       min="{{ now()->format('Y-m-d') }}"
                       class="w-full border-2 border-gray-300 dark:border-gray-600 rounded-xl px-6 py-4 text-lg focus:border-blue-500 dark:focus:border-blue-400 focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-800/30 bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm transition-colors">
            </div>

            {{-- Временные слоты --}}
            <div class="mb-8">
                <div class="flex items-center justify-between mb-6">
                    <label class="block font-semibold text-lg dark:text-white flex items-center gap-2">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Выберите время
                    </label>
                    @if(count($selectedSlots) > 0)
                        <span class="text-sm font-medium text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-900/30 px-3 py-1 rounded-full">
                            Выбрано: {{ count($selectedSlots) }} {{ trans_choice('час|часа|часов', count($selectedSlots)) }}
                        </span>
                    @endif
                </div>

                {{-- Слоты в виде горизонтальной ленты --}}
                <div class="relative">
                    <div class="overflow-x-auto pb-6">
                        <div class="flex gap-4 min-w-max px-2">
                            @foreach($availableSlots as $time => $slot)
                                <button wire:click="toggleSlot('{{ $time }}')"
                                        @disabled(!$slot['available'])
                                        class="flex-shrink-0 p-6 rounded-xl border-2 text-center transition-all duration-300 group
                                               min-w-[140px] hover:scale-105 transform
                                               {{ in_array($time, $selectedSlots) 
                                                  ? 'border-blue-500 dark:border-blue-400 bg-blue-500 dark:bg-blue-600 text-white shadow-2xl scale-105' 
                                                  : ($slot['available'] 
                                                     ? 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 hover:border-blue-400 dark:hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:shadow-xl text-gray-900 dark:text-white' 
                                                     : 'border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-700 opacity-50 cursor-not-allowed text-gray-500 dark:text-gray-400') }}">
                                    <div class="font-bold text-2xl mb-2">{{ $time }}</div>
                                    @if($slot['available'])
                                        <div class="text-lg font-semibold {{ in_array($time, $selectedSlots) ? 'text-blue-100' : 'text-gray-600 dark:text-gray-300' }}">
                                            {{ number_format($slot['price'] / 100, 0) }} ₽
                                        </div>
                                    @else
                                        <div class="text-sm font-medium text-red-600 dark:text-red-400 bg-red-100 dark:bg-red-900/30 px-2 py-1 rounded">
                                            Занято
                                        </div>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>
                    
                    {{-- Градиенты для индикации прокрутки --}}
                    <div class="absolute left-0 top-0 bottom-6 w-12 bg-gradient-to-r from-white dark:from-gray-900 to-transparent pointer-events-none"></div>
                    <div class="absolute right-0 top-0 bottom-6 w-12 bg-gradient-to-l from-white dark:from-gray-900 to-transparent pointer-events-none"></div>
                </div>

                <p class="text-sm text-gray-500 dark:text-gray-400 mt-4 flex items-center gap-2 justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"></path>
                    </svg>
                    Прокрутите влево/вправо для выбора времени
                </p>
            </div>

            {{-- Выбранные слоты --}}
            @if(count($selectedSlots) > 0)
                <div class="mb-8 p-6 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-950/40 dark:to-indigo-950/30 rounded-xl border-2 border-blue-200 dark:border-blue-800">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <p class="font-semibold text-blue-900 dark:text-blue-100 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Выбранное время:
                            </p>
                            <div class="flex flex-wrap gap-3">
                                @foreach($selectedSlots as $time)
                                    <span class="inline-flex items-center px-4 py-2 bg-blue-500 dark:bg-blue-600 text-white rounded-full text-sm font-medium group hover:bg-blue-600 dark:hover:bg-blue-500 transition-colors">
                                        {{ $time }}
                                        <button wire:click="toggleSlot('{{ $time }}')" 
                                                class="ml-2 hover:bg-blue-700 dark:hover:bg-blue-400 rounded-full p-1 transition-colors">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                        </button>
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        <div class="ml-6 text-right">
                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-1">Стоимость:</p>
                            <p class="text-3xl font-bold text-green-600 dark:text-green-400">
                                {{ number_format($totalAmount / 100, 0) }} ₽
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Быстрый выбор --}}
            <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Быстрый выбор:
                </p>
                <div class="flex flex-wrap gap-3">
                    <button wire:click="quickSelect(1)" 
                            class="px-6 py-3 bg-white dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 rounded-xl hover:border-blue-500 dark:hover:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-300 text-gray-900 dark:text-white font-medium hover:scale-105">
                        1 час
                    </button>
                    <button wire:click="quickSelect(2)" 
                            class="px-6 py-3 bg-white dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 rounded-xl hover:border-blue-500 dark:hover:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-300 text-gray-900 dark:text-white font-medium hover:scale-105">
                        2 часа
                    </button>
                    <button wire:click="quickSelect(3)" 
                            class="px-6 py-3 bg-white dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 rounded-xl hover:border-blue-500 dark:hover:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-300 text-gray-900 dark:text-white font-medium hover:scale-105">
                        3 часа
                    </button>
                    <button wire:click="clearSlots" 
                            class="px-6 py-3 bg-white dark:bg-gray-700 border-2 border-red-300 dark:border-red-500 text-red-600 dark:text-red-400 rounded-xl hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-300 font-medium hover:scale-105">
                        Очистить
                    </button>
                </div>
            </div>

            {{-- Итого и кнопка продолжить --}}
            <div class="flex justify-between items-center pt-8 border-t-2 border-gray-200 dark:border-gray-700">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">Итого к оплате:</p>
                    <p class="text-4xl font-bold text-green-600 dark:text-green-400">
                        {{ number_format($totalAmount / 100, 0) }} ₽
                    </p>
                </div>
                <button wire:click="proceedToEquipment"
                        @disabled(count($selectedSlots) === 0)
                        class="px-10 py-4 rounded-xl font-semibold text-lg transition-all duration-300 flex items-center gap-3
                               {{ count($selectedSlots) > 0
                                  ? 'bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-600 text-white shadow-lg hover:shadow-xl hover:scale-105' 
                                  : 'bg-gray-300 dark:bg-gray-700 text-gray-500 dark:text-gray-400 cursor-not-allowed shadow-sm' }}">
                    <span>Далее: Доп. услуги</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>
        </div>
    @endif

    {{-- ШАГ 4: Оборудование --}}
    @if($step === 4)
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg dark:shadow-gray-900/30 p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Дополнительное оборудование</h2>
                    <p class="text-gray-600 dark:text-gray-400">Выберите дополнительное оборудование для вашего бронирования</p>
                </div>
                <button wire:click="goBack" 
                        class="flex items-center gap-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-4 py-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors border border-gray-300 dark:border-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Назад
                </button>
            </div>

            @if(count($availableEquipment) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    @foreach($availableEquipment as $eq)
                        <div class="border-2 border-gray-200 dark:border-gray-700 rounded-xl p-6 bg-white dark:bg-gray-800 hover:border-blue-500 dark:hover:border-blue-400 transition-all duration-300 group hover:shadow-lg">
                            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/40 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-2">{{ $eq['name'] }}</h3>
                            <p class="text-gray-600 dark:text-gray-300 text-sm mb-4">{{ $eq['description'] ?? 'Дополнительное оборудование' }}</p>
                            <div class="flex items-center justify-between">
                                <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($eq['price'] / 100, 0) }} ₽</p>
                                <button wire:click="addEquipment({{ $eq['id'] }})"
                                        class="bg-blue-500 dark:bg-blue-600 hover:bg-blue-600 dark:hover:bg-blue-500 text-white px-4 py-2 rounded-lg font-medium transition-all duration-300 hover:scale-105">
                                    + Добавить
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-24 h-24 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-500 dark:text-gray-400 text-lg">Дополнительное оборудование недоступно</p>
                </div>
            @endif

            {{-- Выбранное оборудование --}}
            @if(count($equipment) > 0)
                <div class="mb-8">
                    <h3 class="font-semibold text-xl text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Добавлено оборудование:
                    </h3>
                    <div class="space-y-4">
                        @foreach($equipment as $index => $item)
                            <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-800 p-6 rounded-xl border-2 border-green-200 dark:border-green-800">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900/40 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-lg text-gray-900 dark:text-white">{{ $item['name'] }}</p>
                                        <p class="text-green-600 dark:text-green-400 font-semibold">{{ number_format($item['price'] / 100, 0) }} ₽/шт</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Кол-во:</span>
                                        <input type="number" 
                                               value="{{ $item['qty'] }}"
                                               wire:change="updateEquipmentQty({{ $index }}, $event.target.value)"
                                               min="1" 
                                               class="w-20 border-2 border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-center bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-blue-500 dark:focus:border-blue-400">
                                    </div>
                                    <button wire:click="removeEquipment({{ $index }})"
                                            class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="flex justify-between items-center pt-6 border-t-2 border-gray-200 dark:border-gray-700">
                <button wire:click="skipEquipment" 
                        class="flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white px-6 py-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    Пропустить
                </button>
                <button wire:click="proceedToClientData" 
                        class="bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-600 text-white px-8 py-4 rounded-xl font-semibold text-lg transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-xl flex items-center gap-2">
                    <span>Далее</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>
        </div>
    @endif

    {{-- ШАГ 5: Данные клиента --}}
    @if($step === 5)
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg dark:shadow-gray-900/30 p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Ваши данные</h2>
                    <p class="text-gray-600 dark:text-gray-400">Заполните информацию для бронирования</p>
                </div>
                <button wire:click="goBack" 
                        class="flex items-center gap-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-4 py-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors border border-gray-300 dark:border-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Назад
                </button>
            </div>

            @guest
                <div class="space-y-6 mb-8">
                    <div class="p-6 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-950/40 dark:to-indigo-950/30 rounded-xl border border-blue-200 dark:border-blue-800">
                        <h3 class="font-semibold text-lg text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Личная информация
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block font-medium mb-3 text-gray-900 dark:text-white">Имя *</label>
                                <input type="text" wire:model="guest_name" 
                                       class="w-full border-2 border-gray-300 dark:border-gray-600 rounded-xl px-4 py-3 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:border-blue-500 dark:focus:border-blue-400 focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-800/30 transition-colors"
                                       placeholder="Введите ваше имя">
                                @error('guest_name') 
                                    <span class="text-red-500 dark:text-red-400 text-sm mt-2 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div>
                                <label class="block font-medium mb-3 text-gray-900 dark:text-white">Email *</label>
                                <input type="email" wire:model="guest_email" 
                                       class="w-full border-2 border-gray-300 dark:border-gray-600 rounded-xl px-4 py-3 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:border-blue-500 dark:focus:border-blue-400 focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-800/30 transition-colors"
                                       placeholder="your@email.com">
                                @error('guest_email') 
                                    <span class="text-red-500 dark:text-red-400 text-sm mt-2 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block font-medium mb-3 text-gray-900 dark:text-white">Телефон</label>
                                <input type="tel" wire:model="guest_phone" 
                                       class="w-full border-2 border-gray-300 dark:border-gray-600 rounded-xl px-4 py-3 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:border-blue-500 dark:focus:border-blue-400 focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-800/30 transition-colors"
                                       placeholder="+7 (XXX) XXX-XX-XX">
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="p-6 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-950/40 dark:to-emerald-950/30 rounded-xl border border-green-200 dark:border-green-800 mb-8">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900/40 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-lg font-bold text-gray-900 dark:text-white">Бронирование на имя: {{ auth()->user()->name }}</p>
                            <p class="text-gray-600 dark:text-gray-300">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                </div>
            @endguest

            <div class="mb-8">
                <label class="block font-semibold mb-4 text-lg text-gray-900 dark:text-white flex items-center gap-2">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                    </svg>
                    Комментарий к бронированию
                </label>
                <textarea wire:model="comment" rows="4" 
                          class="w-full border-2 border-gray-300 dark:border-gray-600 rounded-xl px-4 py-3 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:border-blue-500 dark:focus:border-blue-400 focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-800/30 transition-colors resize-none"
                          placeholder="Дополнительные пожелания или комментарии..."></textarea>
            </div>

            {{-- Сводка бронирования --}}
            <div class="mb-8 p-6 bg-gradient-to-r from-gray-50 to-blue-50 dark:from-gray-800 dark:to-blue-950/30 rounded-xl border border-gray-200 dark:border-gray-700">
                <h3 class="font-semibold text-xl text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Сводка бронирования
                </h3>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-600">
                        <span class="text-gray-600 dark:text-gray-300">Заведение:</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $placeData['place']['name'] ?? '' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-600">
                        <span class="text-gray-600 dark:text-gray-300">Стол:</span>
                        <span class="font-semibold text-gray-900 dark:text-white">
                            {{ collect($placeData['resources'] ?? [])->firstWhere('id', $resource_id)['code'] ?? 'N/A' }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-600">
                        <span class="text-gray-600 dark:text-gray-300">Дата:</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-600">
                        <span class="text-gray-600 dark:text-gray-300">Время:</span>
                        <span class="font-semibold text-gray-900 dark:text-white">
                            {{ implode(', ', $selectedSlots) }}
                        </span>
                    </div>
                    @if(count($equipment) > 0)
                        <div class="py-3 border-b border-gray-200 dark:border-gray-600">
                            <span class="text-gray-600 dark:text-gray-300 block mb-2">Оборудование:</span>
                            <div class="space-y-2">
                                @foreach($equipment as $item)
                                    <div class="flex justify-between">
                                        <span class="text-gray-900 dark:text-white">{{ $item['name'] }} ×{{ $item['qty'] }}</span>
                                        <span class="font-semibold text-green-600 dark:text-green-400">
                                            {{ number_format($item['price'] * $item['qty'] / 100, 0) }} ₽
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    <div class="flex justify-between items-center pt-4">
                        <span class="text-lg font-semibold text-gray-900 dark:text-white">ИТОГО:</span>
                        <span class="text-3xl font-bold text-green-600 dark:text-green-400">
                            {{ number_format($totalAmount / 100, 0) }} ₽
                        </span>
                    </div>
                </div>
            </div>

            <button wire:click="createPendingBooking" 
                    wire:loading.attr="disabled"
                    class="w-full bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-600 text-white px-8 py-4 rounded-xl font-bold text-lg transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-xl flex items-center justify-center gap-3 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                <span wire:loading.remove>Перейти к оплате</span>
                <span wire:loading>Создание бронирования...</span>
                <svg wire:loading.remove class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <svg wire:loading class="w-6 h-6 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
            </button>
        </div>
    @endif

    {{-- ШАГ 6: Оплата --}}
    @if($step === 6 && $booking)
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg dark:shadow-gray-900/30 p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Оплата бронирования</h2>
                    <p class="text-gray-600 dark:text-gray-400">Выберите способ оплаты для подтверждения бронирования</p>
                </div>
                <button wire:click="goBack" 
                        class="flex items-center gap-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-4 py-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors border border-gray-300 dark:border-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Назад
                </button>
            </div>

            {{-- Таймер бронирования --}}
            <div class="mb-8 p-6 bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-yellow-950/40 dark:to-orange-950/30 rounded-xl border-2 border-yellow-200 dark:border-yellow-800">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/40 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-yellow-800 dark:text-yellow-200 mb-1">
                            ⏱ Бронирование действительно в течение <strong>30 минут</strong>
                        </p>
                        <p class="text-sm text-yellow-700 dark:text-yellow-300">
                            Истекает: {{ $booking->expires_at->translatedFormat('d.m.Y в H:i') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Детали бронирования --}}
            <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                <h3 class="font-semibold text-xl text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Детали бронирования
                </h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-300">Номер:</span>
                        <span class="font-semibold text-gray-900 dark:text-white">#{{ $booking->id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-300">Сумма к оплате:</span>
                        <span class="text-2xl font-bold text-green-600 dark:text-green-400">
                            {{ number_format($totalAmount / 100, 2) }} ₽
                        </span>
                    </div>
                </div>
            </div>

            {{-- Способы оплаты --}}
            <div class="space-y-4 mb-8">
                <h3 class="font-semibold text-xl text-gray-900 dark:text-white mb-4">Выберите способ оплаты:</h3>
                
                <button wire:click="payBooking('card')"
                        wire:loading.attr="disabled"
                        class="w-full p-6 border-2 border-gray-300 dark:border-gray-600 rounded-xl hover:border-blue-500 dark:hover:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-300 text-left bg-white dark:bg-gray-800 group hover:scale-105">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/40 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-lg text-gray-900 dark:text-white">💳 Банковская карта</p>
                            <p class="text-gray-600 dark:text-gray-300 text-sm">Visa, Mastercard, Мир</p>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-500 dark:group-hover:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </button>

                <button wire:click="payBooking('online')"
                        wire:loading.attr="disabled"
                        class="w-full p-6 border-2 border-gray-300 dark:border-gray-600 rounded-xl hover:border-blue-500 dark:hover:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-300 text-left bg-white dark:bg-gray-800 group hover:scale-105">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900/40 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-lg text-gray-900 dark:text-white">🌐 Онлайн перевод (СБП)</p>
                            <p class="text-gray-600 dark:text-gray-300 text-sm">Быстрый перевод по номеру телефона</p>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-500 dark:group-hover:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </button>
            </div>

            <button wire:click="skipPayment" 
                    class="w-full text-center text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white py-4 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors font-medium">
                Оплатить позже
            </button>
        </div>
    @endif

    {{-- ШАГ 7: Успех --}}
    @if($step === 7 && $booking)
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg dark:shadow-gray-900/30 p-8 border border-gray-200 dark:border-gray-700 text-center">
            <div class="mb-8">
                <div class="w-24 h-24 bg-green-100 dark:bg-green-900/40 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h2 class="text-4xl font-bold text-green-600 dark:text-green-400 mb-4">
                    @if($booking->isPaid())
                        Оплата прошла успешно!
                    @else
                        Бронирование создано!
                    @endif
                </h2>
                <p class="text-xl text-gray-600 dark:text-gray-300 mb-2">
                    @if($booking->isPaid())
                        Ваше бронирование подтверждено и оплачено
                    @else
                        Бронирование ожидает оплаты
                    @endif
                </p>
                <p class="text-gray-500 dark:text-gray-400">Номер бронирования: <strong>#{{ $booking->id }}</strong></p>
            </div>

            {{-- Детали бронирования --}}
            <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-6 mb-8 text-left border border-gray-200 dark:border-gray-700">
                <h3 class="font-bold text-xl text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Детали бронирования:
                </h3>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-2">
                        <span class="text-gray-600 dark:text-gray-300">Заведение:</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $booking->place->name }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-gray-600 dark:text-gray-300">Стол:</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $booking->resource->code }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-gray-600 dark:text-gray-300">Дата:</span>
                        <span class="font-semibold text-gray-900 dark:text-white">
                            {{ $booking->slots->first()->slot_date ?? 'N/A' }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-gray-600 dark:text-gray-300">Время:</span>
                        <span class="font-semibold text-gray-900 dark:text-white">
                            @foreach($booking->slots as $slot)
                                {{ $slot->slot_time }}@if(!$loop->last), @endif
                            @endforeach
                        </span>
                    </div>
                    <div class="flex justify-between items-center pt-4 border-t border-gray-200 dark:border-gray-600">
                        <span class="text-lg font-bold text-gray-900 dark:text-white">Итого:</span>
                        <span class="text-2xl font-bold text-green-600 dark:text-green-400">
                            {{ $booking->getTotalAmountFormatted() }}
                        </span>
                    </div>
                    @if($booking->isPaid())
                    <div class="flex justify-between items-center pt-2">
                        <span class="text-gray-600 dark:text-gray-300">Статус:</span>
                        <span class="inline-flex items-center px-3 py-1 bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-200 rounded-full text-sm font-medium">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Оплачено ({{ $booking->payment_method }})
                        </span>
                    </div>
                    @endif
                </div>
            </div>

            @if(!$booking->isPaid())
            <div class="mb-8 p-6 bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-yellow-950/40 dark:to-orange-950/30 rounded-xl border-2 border-yellow-200 dark:border-yellow-800">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/40 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div class="text-left">
                        <p class="font-semibold text-yellow-800 dark:text-yellow-200 mb-1">
                            ⚠️ Не забудьте оплатить в течение <strong>30 минут</strong>
                        </p>
                        <p class="text-sm text-yellow-700 dark:text-yellow-300">
                            Иначе бронирование будет автоматически отменено
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <div class="space-y-4">
                <a href="/" 
                   class="block w-full bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-600 text-white px-8 py-4 rounded-xl font-bold text-lg transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-xl text-center">
                    На главную
                </a>
                
                @if(!$booking->isPaid())
                <button wire:click="$set('step', 6)" 
                        class="block w-full bg-green-600 dark:bg-green-700 hover:bg-green-700 dark:hover:bg-green-600 text-white px-8 py-4 rounded-xl font-bold text-lg transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-xl">
                    Оплатить сейчас
                </button>
                @endif
                
                <a 
                   class="block w-full border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:border-blue-500 dark:hover:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 px-8 py-4 rounded-xl font-semibold text-lg transition-all duration-300 hover:scale-105 text-center">
                    Посмотреть детали бронирования
                </a>
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

    /* Темная тема для скроллбара */
    .dark .overflow-x-auto::-webkit-scrollbar-track {
        background: #374151;
        border-radius: 10px;
    }
    
    .dark .overflow-x-auto::-webkit-scrollbar-thumb {
        background: #1D4ED8;
        border-radius: 10px;
    }
    
    .dark .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: #1E40AF;
    }

    /* Плавные переходы для всей темы */
    * {
        transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
    }

    /* Анимация загрузки */
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .animate-spin {
        animation: spin 1s linear infinite;
    }
</style>
@endpush