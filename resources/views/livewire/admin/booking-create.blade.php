<div class="max-w-7xl mx-auto p-6">
    @if (session()->has('error'))
        <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-lg flex items-start">
            <svg class="w-5 h-5 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <span><strong>Ошибка:</strong> {{ session('error') }}</span>
        </div>
    @endif
    @if (session()->has('success'))
        <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-lg">
            <strong>Успех:</strong> {{ session('success') }}
        </div>
    @endif
    @if (session()->has('warning'))
        <div class="mb-6 p-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 rounded-lg">
            {{ session('warning') }}
        </div>
    @endif
    @if (session()->has('info'))
        <div class="mb-6 p-4 bg-blue-100 border-l-4 border-blue-500 text-blue-700 rounded-lg">
            {{ session('info') }}
        </div>
    @endif

    {{-- Прогресс --}}
    <div class="mb-8">
        <div class="flex justify-between items-center">
            @foreach([1 => 'Место', 2 => 'Время', 3 => 'Столы', 4 => 'Доп. услуги', 5 => 'Данные', 6 => 'Оплата', 7 => 'Готово'] as $num => $name)
                <div class="flex items-center {{ $num < 7 ? 'flex-1' : '' }}">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold
                        {{ $step >= $num ? 'bg-blue-500 text-white' : 'bg-gray-300 text-gray-600' }}">
                        @if($num === 7 && $step === 7) ✓ @else {{ $num }} @endif
                    </div>
                    <span class="ml-2 text-sm {{ $step >= $num ? 'text-blue-600 font-medium' : 'text-gray-500' }}">{{ $name }}</span>
                    @if($num < 7)
                        <div class="flex-1 h-1 mx-2 {{ $step > $num ? 'bg-blue-500' : 'bg-gray-300' }}"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- ШАГ 1: Место --}}
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

    {{-- ШАГ 2: Время --}}
    @if($step === 2)
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Выберите время</h2>
                <button wire:click="goBack" class="text-gray-600 hover:text-gray-900 px-4 py-2 rounded hover:bg-gray-100">← Назад</button>
            </div>

            <div class="p-4 bg-blue-50 rounded-lg mb-6">
                <p class="text-sm text-gray-600">Заведение:</p>
                <p class="font-bold text-lg">{{ $placeData['place']['name'] ?? '' }}</p>
            </div>

            <div class="mb-4 p-3 bg-indigo-50 rounded-lg text-sm text-indigo-800">
                Цены указаны <strong>«от»</strong> — минимальная стоимость среди доступных столов.
            </div>

            <div class="mb-6">
                <label class="block font-medium mb-3 text-lg">Дата бронирования</label>
                <input type="date" wire:model.live="date" min="{{ now()->format('Y-m-d') }}"
                       class="border-2 border-gray-300 rounded-lg px-4 py-3 text-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
            </div>

            <div class="mb-6">
                <div class="flex items-center justify-between mb-3">
                    <label class="block font-medium text-lg">Выберите время</label>
                    @if(count($selectedSlots) > 0)
                        <span class="text-sm text-blue-600 font-medium">Выбрано: {{ count($selectedSlots) }} ч.</span>
                    @endif
                </div>
                <div class="overflow-x-auto pb-4">
                    <div class="flex gap-3 min-w-max">
                        @foreach($availableSlots as $time => $slot)
                            <button wire:click="toggleSlot('{{ $time }}')" @disabled(!$slot['available'])
                                    class="flex-shrink-0 p-4 rounded-lg border-2 text-center transition-all min-w-[120px]
                                           {{ in_array($time, $selectedSlots) 
                                              ? 'border-blue-500 bg-blue-500 text-white shadow-lg' 
                                              : ($slot['available'] 
                                                 ? 'border-gray-300 bg-white hover:border-blue-400 hover:bg-blue-50' 
                                                 : 'border-gray-200 bg-gray-100 opacity-50 cursor-not-allowed') }}">
                                <div class="font-bold text-xl mb-1">{{ $time }}</div>
                                @if($slot['available'])
                                    <div class="text-sm {{ in_array($time, $selectedSlots) ? 'text-blue-100' : 'text-gray-600' }}">
                                        от {{ number_format($slot['price'], 0) }} ₽
                                    </div>
                                @else
                                    <div class="text-sm font-medium text-red-600">Занято</div>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <p class="text-sm font-medium text-gray-700 mb-3">Быстрый выбор:</p>
                <div class="flex flex-wrap gap-2">
                    @foreach([1 => '1 час', 2 => '2 часа', 3 => '3 часа'] as $h => $label)
                        <button wire:click="quickSelect({{ $h }})" class="px-4 py-2 bg-white border-2 border-gray-300 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition">{{ $label }}</button>
                    @endforeach
                    <button wire:click="clearSlots" class="px-4 py-2 bg-white border-2 border-red-300 text-red-600 rounded-lg hover:bg-red-50 transition">Очистить</button>
                </div>
            </div>

            <div class="flex justify-end">
                <button wire:click="proceedToTables" @disabled(count($selectedSlots) === 0)
                        class="px-8 py-3 rounded-lg font-medium text-lg transition
                               {{ count($selectedSlots) > 0 ? 'bg-blue-600 hover:bg-blue-700 text-white' : 'bg-gray-300 text-gray-500 cursor-not-allowed' }}">
                    Далее: Выбрать столы →
                </button>
            </div>
        </div>
    @endif

    {{-- ШАГ 3: Столы (мульти) --}}
    @if($step === 3)
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Выберите столы</h2>
                <button wire:click="goBack" class="text-gray-600 hover:text-gray-900 px-4 py-2 rounded hover:bg-gray-100">← Назад</button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="p-3 bg-blue-50 rounded-lg">
                    <p class="text-sm text-gray-600">Заведение</p>
                    <p class="font-bold">{{ $placeData['place']['name'] ?? '' }}</p>
                </div>
                <div class="p-3 bg-teal-50 rounded-lg">
                    <p class="text-sm text-gray-600">Дата</p>
                    <p class="font-bold">{{ $date }}</p>
                </div>
                <div class="p-3 bg-amber-50 rounded-lg">
                    <p class="text-sm text-gray-600">Время</p>
                    <p class="font-bold">{{ implode(', ', $selectedSlots) }}</p>
                </div>
            </div>

            <div class="mb-4 p-3 bg-indigo-50 rounded-lg text-sm text-indigo-800">
                Зелёные столы доступны. Можно выбрать <strong>несколько</strong>. Серые — заняты.
            </div>

            @php
                $gridWidth = $placeData['place']['grid_width'] ?? 20;
                $gridHeight = $placeData['place']['grid_height'] ?? 10;
                $zones = $placeData['zones'] ?? [];
                $resources = $placeData['resources'] ?? [];
            @endphp

            <div class="border-2 border-gray-300 rounded-lg overflow-auto bg-gray-50 mb-6" style="max-height: calc(100vh - 400px);">
                <div class="inline-block min-w-full p-4">
                    <div class="relative grid gap-0" 
                         style="grid-template-columns: repeat({{ $gridWidth }}, minmax(50px, 1fr));
                                grid-template-rows: repeat({{ $gridHeight }}, minmax(50px, 1fr));">
                        @for($y = 0; $y < $gridHeight; $y++)
                            @for($x = 0; $x < $gridWidth; $x++)
                                @php
                                    $cellZone = null;
                                    foreach($zones as $zone) {
                                        $coords = is_string($zone['coordinates']) ? json_decode($zone['coordinates'], true) : ($zone['coordinates'] ?? []);
                                        if (!empty($coords)) {
                                            foreach ($coords as $c) {
                                                if (isset($c['x']) && isset($c['y']) && (int)$c['x'] === $x && (int)$c['y'] === $y) {
                                                    $cellZone = $zone; break 2;
                                                }
                                            }
                                        }
                                    }
                                @endphp
                                <div class="aspect-square border border-gray-200 relative"
                                     style="background-color: {{ $cellZone ? ($cellZone['color'] ?? '#3B82F6') : 'white' }};
                                            opacity: {{ $cellZone ? '0.3' : '1' }}; min-width: 50px; min-height: 50px;"></div>
                            @endfor
                        @endfor

                        @foreach($resources as $resource)
                            @php
                                $rid = $resource['id'];
                                $dw = (($resource['rotation'] ?? 0) === 90 || ($resource['rotation'] ?? 0) === 270) ? ($resource['grid_height'] ?? 1) : ($resource['grid_width'] ?? 1);
                                $dh = (($resource['rotation'] ?? 0) === 90 || ($resource['rotation'] ?? 0) === 270) ? ($resource['grid_width'] ?? 1) : ($resource['grid_height'] ?? 1);
                                $isSel = in_array($rid, $selectedResources);
                                $isAvail = in_array($rid, $availableResourceIds);
                                $price = $resourcePrices[$rid] ?? 0;
                            @endphp
                            <button wire:click="toggleResource({{ $rid }})" @disabled(!$isAvail && !$isSel)
                                    class="absolute flex flex-col items-center justify-center border-3 rounded-lg transition-all
                                           {{ $isSel ? 'border-blue-500 bg-blue-200 shadow-xl ring-4 ring-blue-300 z-20 scale-105' 
                                              : ($isAvail ? 'border-green-600 bg-white hover:border-green-500 hover:bg-green-50 z-10 cursor-pointer' 
                                                 : 'border-gray-400 bg-gray-200 cursor-not-allowed opacity-60 z-10') }}"
                                    style="grid-column: {{ ($resource['grid_x'] ?? 0) + 1 }} / span {{ $dw }};
                                           grid-row: {{ ($resource['grid_y'] ?? 0) + 1 }} / span {{ $dh }}; border-width: 3px;">
                                <div class="text-center pointer-events-none p-1">
                                    <div class="text-sm font-bold {{ $isSel ? 'text-blue-900' : ($isAvail ? 'text-green-900' : 'text-gray-600') }}">
                                        {{ $resource['code'] }}
                                        @if($isSel) ✓ @endif
                                    </div>
                                    @if($isAvail && $price > 0)
                                        <div class="text-xs font-semibold {{ $isSel ? 'text-blue-700' : 'text-green-700' }}">{{ number_format($price, 0) }} ₽</div>
                                    @endif
                                    @if(!$isAvail && !$isSel)
                                        <div class="text-xs text-red-600 font-medium mt-1">Занят</div>
                                    @endif
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            @if(count($selectedResources) > 0)
                <div class="mb-6 p-4 bg-blue-50 rounded-lg border-2 border-blue-200">
                    <p class="font-medium text-blue-900 mb-2">Выбрано столов: {{ count($selectedResources) }}</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($selectedResources as $rid)
                            @php $res = collect($resources)->firstWhere('id', $rid); @endphp
                            @if($res)
                                <span class="inline-flex items-center px-3 py-1 bg-blue-500 text-white rounded-full text-sm font-medium">
                                    {{ $res['code'] }} — {{ number_format($resourcePrices[$rid] ?? 0, 0) }} ₽
                                    <button wire:click="toggleResource({{ $rid }})" class="ml-2 hover:bg-blue-600 rounded-full p-0.5">✕</button>
                                </span>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="flex justify-between items-center pt-6 border-t-2">
                <div>
                    <p class="text-sm text-gray-600">Итого:</p>
                    <p class="text-3xl font-bold text-green-600">{{ number_format($totalAmount, 0) }} ₽</p>
                </div>
                <button wire:click="proceedToEquipment" @disabled(count($selectedResources) === 0)
                        class="px-8 py-3 rounded-lg font-medium text-lg transition
                               {{ count($selectedResources) > 0 ? 'bg-blue-600 hover:bg-blue-700 text-white' : 'bg-gray-300 text-gray-500 cursor-not-allowed' }}">
                    Далее: Доп. услуги →
                </button>
            </div>
        </div>
    @endif

    {{-- ШАГ 4-7 используют те же компоненты, инлайново для admin layout --}}
    @if($step === 4)
        <x-booking.steps.step-equipment 
            :availableEquipment="$availableEquipment" :equipment="$equipment" :totalAmount="$totalAmount"
            wireAddEquipment="addEquipment" wireUpdateEquipmentQty="updateEquipmentQty"
            wireRemoveEquipment="removeEquipment" wireSkipEquipment="skipEquipment"
            wireProceedToClientData="proceedToClientData" wireGoBack="goBack"
        />
    @endif

    @if($step === 5)
        @php $selectedResourcesData = $this->getSelectedResourcesData(); @endphp
        <x-booking.steps.step-client-data-multi
            :placeData="$placeData" :selectedResourcesData="$selectedResourcesData"
            :date="$date" :selectedSlots="$selectedSlots" :equipment="$equipment"
            :totalAmount="$totalAmount" :comment="$comment"
            wireCreatePendingBooking="createPendingBooking" wireGoBack="goBack"
        />
    @endif

    @if($step === 6 && $booking)
        <x-booking.steps.step-payment 
            :booking="$booking" :totalAmount="$totalAmount"
            wirePayBooking="payBooking" wireSkipPayment="skipPayment" wireGoBack="goBack"
        />
    @endif

    @if($step === 7 && $booking)
        <x-booking.steps.step-success-multi :booking="$booking" :totalAmount="$totalAmount" />
    @endif
</div>

@push('styles')
<style>
    .border-3 { border-width: 3px; }
    .overflow-x-auto::-webkit-scrollbar { height: 8px; }
    .overflow-x-auto::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .overflow-x-auto::-webkit-scrollbar-thumb { background: #3B82F6; border-radius: 10px; }
</style>
@endpush
