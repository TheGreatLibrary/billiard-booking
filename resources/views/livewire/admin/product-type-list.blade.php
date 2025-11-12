<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-800">Типы продуктов</h1>
        <a href="{{ route('admin.product-types.create') }}"
           class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
            Добавить тип
        </a>
    </div>

    <div class="bg-white shadow rounded-lg p-4">
        <input type="text" wire:model.live="search"
               placeholder="Поиск по названию..."
               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Название</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Действия</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($types as $type)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $type->id }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $type->name }}</td>
                        <td class="px-6 py-4 text-right text-sm font-medium space-x-2">
                            <a href="{{ route('admin.product-types.edit', $type->id) }}"
                               class="text-blue-600 hover:text-blue-900">Редактировать</a>
                            <button wire:click="delete({{ $type->id }})"
                                    class="text-red-600 hover:text-red-900"
                                    onclick="return confirm('Удалить этот тип продукта?')">
                                Удалить
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                            Нет данных
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-6 py-3">
            {{ $types->links() }}
        </div>
    </div>
</div>
