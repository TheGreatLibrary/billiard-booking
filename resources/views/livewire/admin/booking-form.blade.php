<div>
     <form wire:submit="save">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Пользователь -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-2">Пользователь *</label>
                <select wire:model="user_id" class="w-full border rounded-lg px-3 py-2">
                    <option value="">-- Выберите --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->phone }})</option>
                    @endforeach
                </select>
                @error('user_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Место -->
            <div>
                <label class="block text-sm font-medium mb-2">Адрес *</label>
                <select wire:model.live="place_id" class="w-full border rounded-lg px-3 py-2">
                    <option value="">-- Выберите --</option>
                    @foreach($places as $place)
                        <option value="{{ $place->id }}">{{ $place->name }}</option>
                    @endforeach
                </select>
                @error('place_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Зона -->
            <div>
                <label class="block text-sm font-medium mb-2">Зона *</label>
                <select wire:model.live="zone_id" class="w-full border rounded-lg px-3 py-2" 
                        {{ empty($zones) ? 'disabled' : '' }}>
                    <option value="">{{ empty($zones) ? '-- Сначала выберите адрес --' : '-- Выберите --' }}</option>
                    @foreach($zones as $zone)
                        <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                    @endforeach
                </select>
                @error('zone_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Время -->
            <div>
                <label class="block text-sm font-medium mb-2">Начало *</label>
                <input type="datetime-local" wire:model="starts_at" class="w-full border rounded-lg px-3 py-2">
                @error('starts_at') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Окончание *</label>
                <input type="datetime-local" wire:model="ends_at" class="w-full border rounded-lg px-3 py-2">
                @error('ends_at') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Столы (чекбоксы) -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-2">Столы *</label>
                <div class="border rounded-lg p-4 min-h-[100px]">
                    @if(empty($tables) || count($tables) === 0)
                        <p class="text-gray-400 text-center">Сначала выберите зону</p>
                    @else
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach($tables as $table)
                                <label class="border rounded-lg p-3 hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" wire:model="resource_ids" value="{{ $table['id'] }}" 
                                           class="mr-2">
                                    <span class="font-medium">{{ $table['name'] }}</span>
                                    <div class="text-sm text-gray-500">{{ $table['description'] }}</div>
                                </label>
                            @endforeach
                        </div>
                    @endif
                </div>
                @error('resource_ids') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Оборудование -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-2">Дополнительное оборудование</label>
                
                @if(!empty($equipment) && count($equipment) > 0)
                    <div class="grid grid-cols-3 gap-3 mb-4">
                        @foreach($equipment as $eq)
                            <div class="border rounded-lg p-3">
                                <div class="font-medium">{{ $eq->name }}</div>
                                <div class="text-sm text-gray-500">{{ $eq->base_price_hour }} ₽/час</div>
                                <button type="button" 
                                        wire:click="addEquipment({{ $eq->id }})"
                                        class="mt-2 w-full bg-blue-500 text-white px-3 py-1 rounded text-sm">
                                    + Добавить
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Выбранное оборудование -->
                @foreach($selectedEquipment as $index => $item)
                    <div class="flex space-x-2 items-center bg-blue-50 p-3 rounded-lg mb-2">
                        <div class="flex-1">
                            <div class="font-medium">{{ $item['name'] }}</div>
                            <div class="text-sm">{{ $item['price'] }} ₽/час</div>
                        </div>
                        <input type="number" wire:model="selectedEquipment.{{ $index }}.qty" 
                               min="1" class="w-20 border rounded px-2 py-1">
                        <button type="button" wire:click="removeEquipment({{ $index }})" 
                                class="bg-red-500 text-white px-3 py-2 rounded">✕</button>
                    </div>
                @endforeach
            </div>

            <!-- Статус -->
            <div>
                <label class="block text-sm font-medium mb-2">Статус *</label>
                <select wire:model="status" class="w-full border rounded-lg px-3 py-2">
                    <option value="pending">Ожидание</option>
                    <option value="confirmed">Подтверждено</option>
                    <option value="canceled">Отменено</option>
                </select>
            </div>

            <!-- Заметки -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-2">Заметки</label>
                <textarea wire:model="notes" rows="3" class="w-full border rounded-lg px-3 py-2"></textarea>
            </div>
        </div>

        <div class="mt-6 flex space-x-3">
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg">
                💾 Создать бронирование
            </button>
            <a href="{{ route('admin.bookings.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg">
                Отмена
            </a>
        </div>
    </form>
</div>
