<div class="space-y-4">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold">Ценовые правила</h1>
        <a href="{{ route('admin.price-rules.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
            Добавить правило
        </a>
    </div>

    <div class="bg-white shadow rounded-lg p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
            <div>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Поиск..."
                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <select wire:model.live="place_id"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Все места</option>
                    @foreach($places as $place)
                        <option value="{{ $place->id }}">{{ $place->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select wire:model.live="kind"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Все типы</option>
                    <option value="coef">Множитель</option>
                    <option value="override">Фикс. цена</option>
                </select>
            </div>
            <div>
                <select wire:model.live="active"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Все статусы</option>
                    <option value="1">Активные</option>
                    <option value="0">Неактивные</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Место</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Зона</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">День</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Время</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Тип</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Значение</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Активно</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Действия</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($rules as $rule)
                        <tr>
                            <td class="px-4 py-3 text-sm">{{ $rule->id }}</td>
                            <td class="px-4 py-3 text-sm">{{ $rule->place->name ?? '' }}</td>
                            <td class="px-4 py-3 text-sm">{{ $rule->zone?->name ?? 'Все' }}</td>
                            <td class="px-4 py-3 text-sm">{{ $rule->dow ?? 'Все' }}</td>
                            <td class="px-4 py-3 text-sm">
                                {{ $rule->time_from ?? '—' }} - {{ $rule->time_to ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ $rule->kind === 'coef' ? 'Множитель' : 'Фикс. цена' }}
                            </td>
                            <td class="px-4 py-3 text-sm">{{ $rule->value }}</td>
                            <td class="px-4 py-3 text-sm">
                                {{ $rule->active ? '✅' : '❌' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-right space-x-2">
                                <a href="{{ route('admin.price-rules.edit', $rule) }}" 
                                   class="text-blue-600 hover:text-blue-900">Изменить</a>
                                <button wire:click="delete({{ $rule->id }})" 
                                        wire:confirm="Удалить это правило?"
                                        class="text-red-600 hover:text-red-900">Удалить</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                                Правила не найдены
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $rules->links() }}
        </div>
    </div>
</div>