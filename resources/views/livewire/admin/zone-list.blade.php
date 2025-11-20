<div class="space-y-6">
    <!-- Заголовок и кнопка -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-800">Список зон</h1>
        <a href="{{ route('admin.zones.create') }}"
           class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
            Добавить зону
        </a>
    </div>

    <!-- Фильтр -->
    <div class="bg-white shadow rounded-lg p-4 flex items-center space-x-4">
        <div class="w-1/3">
            <input type="text" wire:model.live="search"
                   placeholder="Поиск по названию..."
                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div class="w-1/3">
            <select wire:model.live="place_id"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="">Все заведения</option>
                @foreach($places as $place)
                    <option value="{{ $place->id }}">{{ $place->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Таблица -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Название</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Заведение</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Коэффициент цены</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Действия</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($zones as $zone)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $zone->id }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $zone->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $zone->place->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $zone->price_coef }}</td>
                        <td class="px-6 py-4 text-right text-sm font-medium space-x-2">
                            <a href="{{ route('admin.zones.edit', $zone->id) }}"
                               class="text-blue-600 hover:text-blue-900">Редактировать</a>
                            <button wire:click="delete({{ $zone->id }})"
                                    class="text-red-600 hover:text-red-900"
                                    onclick="return confirm('Удалить эту зону?')">Удалить</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            Нет данных
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-6 py-3">
            {{ $zones->links() }}
        </div>
    </div>
</div>
