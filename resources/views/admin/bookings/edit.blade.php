@extends('admin.layout.app')

@section('title', 'Редактирование бронирования #' . $booking->id)

@section('content')
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
        <form action="{{ route('admin.bookings.update', $booking) }}" method="POST" id="booking-form">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Пользователь -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Пользователь *</label>
                    <select name="user_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                        <option value="">-- Выберите пользователя --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" 
                                {{ (old('user_id', $booking->user_id) == $user->id) ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Place -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Адрес *</label>
                    <select name="place_id" id="place_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                        <option value="">-- Выберите адрес Бильярдной --</option>
                        @foreach($places as $place)
                            <option value="{{ $place->id }}" 
                                {{ (old('place_id', $booking->place_id) == $place->id) ? 'selected' : '' }}>
                                {{ $place->name }} - {{ $place->description ?? 'Главный клуб' }}
                            </option>
                        @endforeach
                    </select>
                    @error('place_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Zone -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Зона *</label>
                    <select name="zone_id" id="zone_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                        <option value="">-- Выберите зону --</option>
                        @foreach($zones as $zone)
                            <option value="{{ $zone->id }}" 
                                {{ (old('zone_id', $booking->bookingResources->first()->resource->zone_id ?? null) == $zone->id) ? 'selected' : '' }}>
                                {{ $zone->name }} - {{ $zone->description ?? 'Основная зона' }}
                            </option>
                        @endforeach
                    </select>
                    @error('zone_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Стол -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Стол *</label>
                    <select name="resource_id" id="resource_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                        <option value="">-- Выберите стол --</option>
                        @foreach($tables as $table)
                            <option value="{{ $table['id'] }}" 
                                {{ (old('resource_id', $booking->bookingResources->first()->resource_id ?? null) == $table['id']) ? 'selected' : '' }}>
                                {{ $table['name'] }} - {{ $table['description'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('resource_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Дата и время -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Начало *</label>
                    <input type="datetime-local" name="starts_at" 
                           value="{{ old('starts_at', \Carbon\Carbon::parse($booking->starts_at)->format('Y-m-d\TH:i')) }}"
                           required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                    @error('starts_at') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Окончание *</label>
                    <input type="datetime-local" name="ends_at" 
                           value="{{ old('ends_at', \Carbon\Carbon::parse($booking->ends_at)->format('Y-m-d\TH:i')) }}"
                           required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                    @error('ends_at') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Статус -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Статус *</label>
                    <select name="status" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                        <option value="pending" {{ old('status', $booking->status) == 'pending' ? 'selected' : '' }}>Ожидание</option>
                        <option value="confirmed" {{ old('status', $booking->status) == 'confirmed' ? 'selected' : '' }}>Подтверждено</option>
                        <option value="canceled" {{ old('status', $booking->status) == 'canceled' ? 'selected' : '' }}>Отменено</option>
                        <option value="finished" {{ old('status', $booking->status) == 'finished' ? 'selected' : '' }}>Завершено</option>
                        <option value="no_show" {{ old('status', $booking->status) == 'no_show' ? 'selected' : '' }}>Не пришёл</option>
                    </select>
                    @error('status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Заметки -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Заметки</label>
                    <textarea name="notes" rows="3" 
                              placeholder="Дополнительная информация..."
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">{{ old('notes', $booking->comment) }}</textarea>
                    @error('notes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mt-8 flex space-x-3">
                <button type="submit" 
                        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg font-medium">
                    💾 Сохранить изменения
                </button>
                <a href="{{ route('admin.bookings.show', $booking) }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-medium">
                    Отмена
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const placeSelect = document.getElementById('place_id');
    const zoneSelect = document.getElementById('zone_id');
    const resourceSelect = document.getElementById('resource_id');

    // Функция для загрузки зон
    function loadZones(placeId) {
        if (!placeId) {
            zoneSelect.innerHTML = '<option value="">-- Сначала выберите адрес --</option>';
            zoneSelect.disabled = true;
            resourceSelect.innerHTML = '<option value="">-- Сначала выберите зону --</option>';
            resourceSelect.disabled = true;
            return;
        }

        const currentZoneId = zoneSelect.value;
        zoneSelect.innerHTML = '<option value="">-- Загрузка зон...</option>';
        zoneSelect.disabled = true;

        fetch(`/admin/bookings/zones/${placeId}`)
            .then(response => response.json())
            .then(zones => {
                zoneSelect.innerHTML = '<option value="">-- Выберите зону --</option>';
                zones.forEach(zone => {
                    const selected = zone.id == currentZoneId ? 'selected' : '';
                    zoneSelect.innerHTML += `<option value="${zone.id}" ${selected}>${zone.name} - ${zone.description || 'Основная зона'}</option>`;
                });
                zoneSelect.disabled = false;
                
                if (currentZoneId) {
                    loadTables(currentZoneId);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                zoneSelect.innerHTML = '<option value="">-- Ошибка загрузки --</option>';
            });
    }

    // Функция для загрузки столов
    function loadTables(zoneId) {
        if (!zoneId) {
            resourceSelect.innerHTML = '<option value="">-- Сначала выберите зону --</option>';
            resourceSelect.disabled = true;
            return;
        }

        const currentResourceId = resourceSelect.value;
        resourceSelect.innerHTML = '<option value="">-- Загрузка столов...</option>';
        resourceSelect.disabled = true;

        fetch(`/admin/bookings/tables/${zoneId}`)
            .then(response => response.json())
            .then(tables => {
                resourceSelect.innerHTML = '<option value="">-- Выберите стол --</option>';
                tables.forEach(table => {
                    const selected = table.id == currentResourceId ? 'selected' : '';
                    resourceSelect.innerHTML += `<option value="${table.id}" ${selected}>${table.name} - ${table.description}</option>`;
                });
                resourceSelect.disabled = false;
            })
            .catch(error => {
                console.error('Error:', error);
                resourceSelect.innerHTML = '<option value="">-- Ошибка загрузки --</option>';
            });
    }

    // Обработчики событий
    placeSelect.addEventListener('change', function() {
        loadZones(this.value);
    });

    zoneSelect.addEventListener('change', function() {
        loadTables(this.value);
    });
});
</script>
@endsection