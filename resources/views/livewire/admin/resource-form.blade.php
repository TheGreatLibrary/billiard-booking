<div class="max-w-4xl mx-auto bg-white shadow rounded-lg p-6 space-y-6">
    <form wire:submit.prevent="save" class="space-y-6">
        <!-- Тип ресурса -->
        <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Тип ресурса</h3>
            <div class="flex gap-4">
                <label class="flex items-center cursor-pointer">
                    <input type="radio" wire:model.live="type" value="table" 
                           class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                    <span class="ml-2 text-sm font-medium text-gray-700">
                        🎯 Стол (физический объект с координатами)
                    </span>
                </label>
                <label class="flex items-center cursor-pointer">
                    <input type="radio" wire:model.live="type" value="equipment" 
                           class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                    <span class="ml-2 text-sm font-medium text-gray-700">
                        🎱 Инвентарь (кий, мел, перчатка и т.д.)
                    </span>
                </label>
            </div>
            @error('type') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Основная информация -->
        <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Основная информация</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Модель *</label>
                    <select wire:model="model_id"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Выберите модель</option>
                        @foreach($models as $model)
                            <option value="{{ $model->id }}">{{ $model->name }}</option>
                        @endforeach
                    </select>
                    @error('model_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Место</label>
                    <select wire:model="place_id"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Выберите место</option>
                        @foreach($places as $place)
                            <option value="{{ $place->id }}">{{ $place->name }}</option>
                        @endforeach
                    </select>
                    @error('place_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                @if($type === 'table')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Зона *</label>
                    <select wire:model="zone_id"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Выберите зону</option>
                        @foreach($zones as $zone)
                            <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                        @endforeach
                    </select>
                    @error('zone_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Состояние *</label>
                    <select wire:model="state_id"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Выберите состояние</option>
                        @foreach($states as $state)
                            <option value="{{ $state->id }}">{{ $state->name }}</option>
                        @endforeach
                    </select>
                    @error('state_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Код</label>
                    <input type="text" wire:model="code"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Например: T-01">
                    @error('code') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                @if($type === 'equipment')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Количество *</label>
                    <input type="number" wire:model="quantity" min="1"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Количество единиц в наличии">
                    @error('quantity') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    <p class="text-xs text-gray-500 mt-1">Общее количество единиц инвентаря</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Позиция на сетке (только для столов) -->
        @if($type === 'table')
        <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Позиция на сетке</h3>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">X *</label>
                    <input type="number" wire:model="grid_x" min="0"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('grid_x') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Y *</label>
                    <input type="number" wire:model="grid_y" min="0"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('grid_y') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ширина</label>
                    <input type="number" wire:model="grid_width" min="1"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('grid_width') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Высота</label>
                    <input type="number" wire:model="grid_height" min="1"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('grid_height') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Поворот (°)</label>
                    <input type="number" wire:model="rotation" min="0" max="360" step="90"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('rotation') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">
                Координаты и размеры используются для визуального редактора размещения ресурсов
            </p>
        </div>
        @endif

        <!-- Примечание -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Примечание</label>
            <textarea wire:model="note" rows="3"
                      class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                      placeholder="Дополнительная информация о ресурсе"></textarea>
            @error('note') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Кнопки -->
        <div class="flex justify-end space-x-3 pt-4 border-t">
            <a href="{{ route('admin.resources.index') }}" 
               class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md transition-colors">
                Отмена
            </a>
            <button type="submit" 
                    class="px-4 py-2 bg-blue-600 text-white hover:bg-blue-700 rounded-md transition-colors">
                {{ $resourceId ? 'Обновить' : 'Создать' }}
            </button>
        </div>
    </form>
</div>