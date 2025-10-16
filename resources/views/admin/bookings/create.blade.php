@extends('admin.layout.app')

@section('title', 'Новое бронирование')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Новое бронирование</h1>
        <p class="text-gray-600">Создание нового бронирования стола</p>
    </div>
    <a href="{{ route('admin.bookings.index') }}" 
       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
        ← Назад к списку
    </a>
</div>

<div class="max-w-4xl">
    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('admin.bookings.store') }}" method="POST" id="booking-form">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Пользователь -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Пользователь *</label>
                    <select name="user_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                        <option value="">-- Выберите пользователя --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
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
                            <option value="{{ $place->id }}" {{ old('place_id') == $place->id ? 'selected' : '' }}>
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
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500"
                            {{ old('zone_id') ? '' : 'disabled' }}>
                        <option value="">-- Сначала выберите адрес --</option>
                        @if(old('zone_id'))
                            @php
                                $oldZone = \App\Models\Zone::find(old('zone_id'));
                            @endphp
                            @if($oldZone)
                                <option value="{{ $oldZone->id }}" selected>
                                    {{ $oldZone->name }} - {{ $oldZone->description ?? 'Основная зона' }}
                                </option>
                            @endif
                        @endif
                    </select>
                    @error('zone_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Стол -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Стол *</label>
                    <select name="resource_id" id="resource_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500"
                            {{ old('resource_id') ? '' : 'disabled' }}>
                        <option value="">-- Сначала выберите зону --</option>
                        @if(old('resource_id'))
                            @php
                                $oldTable = \App\Models\Resource::find(old('resource_id'));
                            @endphp
                            @if($oldTable)
                                <option value="{{ $oldTable->id }}" selected>
                                    {{ $oldTable->name }} - {{ $oldTable->description ?? 'Стол для бильярда' }}
                                </option>
                            @endif
                        @endif
                    </select>
                    @error('resource_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Дата и время -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Начало *</label>
                    <input type="datetime-local" name="starts_at" 
                           value="{{ old('starts_at') }}"
                           min="{{ now()->format('Y-m-d\TH:i') }}"
                           required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                    @error('starts_at') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Окончание *</label>
                    <input type="datetime-local" name="ends_at" 
                           value="{{ old('ends_at') }}"
                           required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                    @error('ends_at') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Статус -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Статус *</label>
                    <select name="status" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Ожидание</option>
                        <option value="confirmed" {{ old('status') == 'confirmed' ? 'selected' : '' }}>Подтверждено</option>
                        <option value="canceled" {{ old('status') == 'canceled' ? 'selected' : '' }}>Отменено</option>
                    </select>
                    @error('status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Заметки -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Заметки</label>
                    <textarea name="notes" rows="3" 
                              placeholder="Дополнительная информация..."
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">{{ old('notes') }}</textarea>
                    @error('notes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mt-8 flex space-x-3">
                <button type="submit" 
                        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg font-medium">
                    💾 Создать бронирование
                </button>
                <a href="{{ route('admin.bookings.index') }}" 
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

        zoneSelect.innerHTML = '<option value="">-- Загрузка зон...</option>';
        zoneSelect.disabled = true;

        fetch(`/admin/bookings/zones/${placeId}`)
            .then(response => {
                if (!response.ok) throw new Error('Network error');
                return response.json();
            })
            .then(zones => {
                zoneSelect.innerHTML = '<option value="">-- Выберите зону --</option>';
                zones.forEach(zone => {
                    zoneSelect.innerHTML += `<option value="${zone.id}">${zone.name} - ${zone.description || 'Основная зона'}</option>`;
                });
                zoneSelect.disabled = false;
                
                // Восстанавливаем выбранную зону если есть
                @if(old('zone_id'))
                    zoneSelect.value = '{{ old('zone_id') }}';
                    if (zoneSelect.value) {
                        loadTables(zoneSelect.value);
                    }
                @endif
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

        resourceSelect.innerHTML = '<option value="">-- Загрузка столов...</option>';
        resourceSelect.disabled = true;

        fetch(`/admin/bookings/tables/${zoneId}`)
            .then(response => {
                if (!response.ok) throw new Error('Network error');
                return response.json();
            })
            .then(tables => {
    resourceSelect.innerHTML = '<option value="">-- Выберите стол --</option>';
    tables.forEach(table => {
        resourceSelect.innerHTML += `<option value="${table.id}">${table.name} - ${table.description}</option>`;
    });
    resourceSelect.disabled = false;
    
    // Восстанавливаем выбранный стол если есть
    @if(old('resource_id'))
        resourceSelect.value = '{{ old('resource_id') }}';
    @endif
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

    // Инициализация при загрузке
    @if(old('place_id'))
        loadZones('{{ old('place_id') }}');
    @endif
});
</script>
@endsection