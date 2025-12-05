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
                   placeholder="Поиск по коду или модели"
                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div class="flex-1 min-w-[200px]">
            <select wire:model.live="type"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="">Все типы</option>
                <option value="table">🎯 Столы</option>
                <option value="equipment">🎱 Инвентарь</option>
            </select>
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
        <table class="w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Тип</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Модель</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Код</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Зона</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Кол-во</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Состояние</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Действия</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($resources as $resource)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm">
                            @if($resource->type === 'table')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    🎯 Стол
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    🎱 Инвентарь
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $resource->productModel->name ?? '—' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                            {{ $resource->code ?? '—' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            @if($resource->zone)
                                <span class="text-gray-600">{{ $resource->zone->name }}</span>
                            @else
                                <span class="text-gray-400 italic">Не привязан</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            @if($resource->type === 'equipment')
                                <span class="font-semibold text-blue-600">{{ $resource->quantity }} шт.</span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if($resource->state)
                                @if($resource->state->name === 'active')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Активен
                                    </span>
                                @elseif($resource->state->name === 'maintenance')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        На обслуживании
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $resource->state->name }}
                                    </span>
                                @endif
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
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
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                <p class="text-lg font-medium">Ресурсы не найдены</p>
                                <p class="text-sm mt-1">Попробуйте изменить фильтры или добавьте новый ресурс</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($resources->hasPages())
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $resources->links() }}
        </div>
        @endif
    </div>

    <!-- Статистика -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white shadow rounded-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Всего ресурсов</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $resources->total() }}</p>
                </div>
            </div>
        </div>
    </div>
</div>