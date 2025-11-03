@extends('admin.layout.app')

@section('title', 'Новое бронирование')

@section('content')

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Новое бронирование</h1>
        <p class="text-gray-600">Создание нового бронирования столов</p>
    </div>
    <a href="{{ route('admin.bookings.index') }}" 
       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
        ← Назад к списку
    </a>
</div>

<div class="max-w-6xl">
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
                                {{ $user->name }} ({{ $user->phone }})
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
                        <option value="">-- Выберите адрес --</option>
                        @foreach($places as $place)
                            <option value="{{ $place->id }}" {{ old('place_id') == $place->id ? 'selected' : '' }}>
                                {{ $place->name }}
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
                            disabled>
                        <option value="">-- Сначала выберите адрес --</option>
                    </select>
                    @error('zone_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Дата и время -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Начало *</label>
                    <input type="datetime-local" name="starts_at" id="starts_at"
                           value="{{ old('starts_at') }}"
                           min="{{ now()->format('Y-m-d\TH:i') }}"
                           required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                    @error('starts_at') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Окончание *</label>
                    <input type="datetime-local" name="ends_at" id="ends_at"
                           value="{{ old('ends_at') }}"
                           required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                    @error('ends_at') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Столы (множественный выбор) -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Столы *</label>
                    <div id="tables-container" class="border border-gray-300 rounded-lg p-4 min-h-[100px]">
                        <p class="text-gray-400 text-center">Сначала выберите зону</p>
                    </div>
                    @error('resource_ids') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    @error('resource_ids.*') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Дополнительное оборудование</label>
                <div id="equipment-list-container" class="border border-gray-300 rounded-lg p-4 min-h-[100px]">
                    <p class="text-gray-400 text-center">Сначала выберите место</p>
                </div>
                <div id="equipment-container" class="space-y-2 mt-2">
                    <!-- Сюда будут добавляться строки с выбором оборудования -->
                </div>
                </div>

                <!-- Статус -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Статус *</label>
                    <select name="status" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Ожидание</option>
                        <option value="confirmed" {{ old('status', 'confirmed') == 'confirmed' ? 'selected' : '' }}>Подтверждено</option>
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
    const tablesContainer = document.getElementById('tables-container');
    const equipmentListContainer = document.getElementById('equipment-list-container');
    const equipmentContainer = document.getElementById('equipment-container');

    let availableTables = [];
    let availableEquipment = [];
    let equipmentCounter = 0;

    // Загрузка зон и оборудования
    placeSelect.addEventListener('change', function() {
        const placeId = this.value;
        
        if (!placeId) {
            zoneSelect.innerHTML = '<option value="">-- Сначала выберите адрес --</option>';
            zoneSelect.disabled = true;
            tablesContainer.innerHTML = '<p class="text-gray-400 text-center">Сначала выберите зону</p>';
            equipmentListContainer.innerHTML = '<p class="text-gray-400 text-center">Сначала выберите место</p>';
            equipmentContainer.innerHTML = '';
            availableEquipment = [];
            return;
        }

        zoneSelect.innerHTML = '<option value="">-- Загрузка зон...</option>';
        zoneSelect.disabled = true;
        equipmentListContainer.innerHTML = '<p class="text-gray-400 text-center">Загрузка оборудования...</p>';

        // Загружаем зоны
        fetch(`/admin/bookings/zones/${placeId}`)
            .then(response => response.json())
            .then(zones => {
                zoneSelect.innerHTML = '<option value="">-- Выберите зону --</option>';
                zones.forEach(zone => {
                    zoneSelect.innerHTML += `<option value="${zone.id}">${zone.name}</option>`;
                });
                zoneSelect.disabled = false;
            })
            .catch(error => {
                console.error('Error:', error);
                zoneSelect.innerHTML = '<option value="">-- Ошибка загрузки --</option>';
            });
        
        // Загружаем оборудование
        fetch(`/admin/bookings/equipment/${placeId}`)
            .then(response => response.json())
            .then(equipment => {
                availableEquipment = equipment;
                renderEquipmentList();
            })
            .catch(error => {
                console.error('Error loading equipment:', error);
                equipmentListContainer.innerHTML = '<p class="text-red-500 text-center">Ошибка загрузки оборудования</p>';
            });
    });

    // Загрузка столов
    zoneSelect.addEventListener('change', function() {
        const zoneId = this.value;
        
        if (!zoneId) {
            tablesContainer.innerHTML = '<p class="text-gray-400 text-center">Сначала выберите зону</p>';
            return;
        }

        tablesContainer.innerHTML = '<p class="text-gray-400 text-center">Загрузка столов...</p>';

        fetch(`/admin/bookings/tables/${zoneId}`)
            .then(response => response.json())
            .then(tables => {
                availableTables = tables;
                renderTables();
            })
            .catch(error => {
                console.error('Error:', error);
                tablesContainer.innerHTML = '<p class="text-red-500 text-center">Ошибка загрузки столов</p>';
            });
    });

    // Отрисовка столов с чекбоксами
    function renderTables() {
        if (availableTables.length === 0) {
            tablesContainer.innerHTML = '<p class="text-gray-400 text-center">Нет доступных столов</p>';
            return;
        }

        tablesContainer.innerHTML = '<div class="grid grid-cols-2 md:grid-cols-3 gap-3"></div>';
        const grid = tablesContainer.querySelector('div');

        availableTables.forEach(table => {
            const div = document.createElement('div');
            div.className = 'border border-gray-300 rounded-lg p-3 hover:bg-gray-50 cursor-pointer';
            div.innerHTML = `
                <label class="flex items-start space-x-2 cursor-pointer">
                    <input type="checkbox" name="resource_ids[]" value="${table.id}" 
                           class="mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <div class="flex-1">
                        <div class="font-medium text-gray-900">${table.name}</div>
                        <div class="text-sm text-gray-500">${table.description}</div>
                    </div>
                </label>
            `;
            grid.appendChild(div);
        });
    }

    // Отрисовка списка доступного оборудования
    function renderEquipmentList() {
        if (availableEquipment.length === 0) {
            equipmentListContainer.innerHTML = '<p class="text-gray-400 text-center">Нет доступного оборудования</p>';
            return;
        }

        equipmentListContainer.innerHTML = '<div class="grid grid-cols-2 md:grid-cols-3 gap-3"></div>';
        const grid = equipmentListContainer.querySelector('div');

        availableEquipment.forEach(eq => {
            const div = document.createElement('div');
            div.className = 'border border-gray-300 rounded-lg p-3 hover:bg-blue-50 cursor-pointer';
            div.innerHTML = `
                <div class="font-medium text-gray-900">${eq.name}</div>
                <div class="text-sm text-gray-500">${eq.price} ₽/час</div>
                <button type="button" 
                        onclick="addEquipmentItem(${eq.id}, '${eq.name}', ${eq.price})"
                        class="mt-2 w-full bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm">
                    + Добавить
                </button>
            `;
            grid.appendChild(div);
        });
    }

    // Добавление оборудования в заказ
    window.addEquipmentItem = function(id, name, price) {
        equipmentCounter++;
        const row = document.createElement('div');
        row.className = 'flex space-x-2 items-center bg-blue-50 p-3 rounded-lg';
        row.innerHTML = `
            <input type="hidden" name="equipment[${equipmentCounter}][model_id]" value="${id}">
            <div class="flex-1">
                <div class="font-medium">${name}</div>
                <div class="text-sm text-gray-600">${price} ₽/час</div>
            </div>
            <input type="number" name="equipment[${equipmentCounter}][qty]" value="1" min="1" 
                   class="w-20 border border-gray-300 rounded-lg px-3 py-2" placeholder="Кол-во">
            <button type="button" onclick="this.parentElement.remove()" 
                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg">
                ✕
            </button>
        `;
        equipmentContainer.appendChild(row);
    };
});
</script>
@endsection