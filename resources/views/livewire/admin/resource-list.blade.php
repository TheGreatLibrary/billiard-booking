<div class="space-y-6">
    <!-- Заголовок -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-800">Список ресурсов</h1>
        <a href="{{ route('admin.resources.create') }}"
           class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
            Добавить ресурс
        </a>
    </div>

    <!-- Фильтры -->
    <div class="bg-white shadow rounded-lg p-4 flex flex-wrap gap-4">
        <div class="flex-1 min-w-[200px]">
            <input type="text" wire:model.live="search"
                   placeholder="Поиск по коду"
                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div class="flex-1 min-w-[200px]">
            <select wire:model.live="model_id"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="">Все модели</option>
                @foreach($models as $model)
                    <option value="{{ $model->id }}">{{ $model->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex-1 min-w-[200px]">
            <select wire:model.live="zone_id"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="">Все зоны</option>
                @foreach($zones as $zone)
                    <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Таблица -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Модель</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Зона</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Состояние</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Код</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Действия</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($resources as $resource)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $resource->id }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $resource->model->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $resource->zone->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $resource->state->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $resource->code ?? '—' }}</td>
                        <td class="px-6 py-4 text-right text-sm font-medium space-x-2">
                            <a href="{{ route('admin.resources.edit', $resource->id) }}"
                               class="text-blue-600 hover:text-blue-900">Редактировать</a>
                            <button wire:click="delete({{ $resource->id }})"
                                    class="text-red-600 hover:text-red-900"
                                    onclick="return confirm('Удалить ресурс?')">Удалить</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            Нет данных
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-6 py-3">
            {{ $resources->links() }}
        </div>
    </div>
</div>
