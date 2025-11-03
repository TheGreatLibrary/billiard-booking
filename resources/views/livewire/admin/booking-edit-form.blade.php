<div>
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Редактирование бронирования #{{ $booking->id }}</h1>
            <p class="text-gray-600">Изменение данных бронирования</p>
        </div>
        <a href="{{ route('admin.bookings.show', $booking) }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
            ← Назад
        </a>
    </div>

    <div class="max-w-4xl">
        <div class="bg-white rounded-lg shadow-md p-6">
            <form wire:submit="update">
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
                        <select wire:model.live="zone_id" class="w-full border rounded-lg px-3 py-2">
                            <option value="">-- Выберите --</option>
                            @foreach($zones as $zone)
                                <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                            @endforeach
                        </select>
                        @error('zone_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Стол -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-2">Стол *</label>
                        <select wire:model="resource_id" class="w-full border rounded-lg px-3 py-2">
                            <option value="">-- Выберите --</option>
                            @foreach($tables as $table)
                                <option value="{{ $table['id'] }}">
                                    {{ $table['name'] }} - {{ $table['description'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('resource_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
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

                    <!-- Статус -->
                    <div>
                        <label class="block text-sm font-medium mb-2">Статус *</label>
                        <select wire:model="status" class="w-full border rounded-lg px-3 py-2">
                            <option value="pending">Ожидание</option>
                            <option value="confirmed">Подтверждено</option>
                            <option value="canceled">Отменено</option>
                            <option value="finished">Завершено</option>
                            <option value="no_show">Не пришёл</option>
                        </select>
                        @error('status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Заметки -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-2">Заметки</label>
                        <textarea wire:model="notes" rows="3" class="w-full border rounded-lg px-3 py-2"></textarea>
                        @error('notes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-6 flex space-x-3">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg">
                        💾 Сохранить изменения
                    </button>
                    <a href="{{ route('admin.bookings.show', $booking) }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg">
                        Отмена
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
