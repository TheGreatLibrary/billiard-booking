<div class="space-y-4">
    <div class="flex justify-between items-center">
        <h1 class="text-xl font-bold">Модели продуктов</h1>
        <a href="{{ route('admin.product-models.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Добавить модель
        </a>
    </div>



    <div class="bg-white shadow rounded-lg p-4 space-y-4">
        <div class="flex gap-4">
            <input type="text" wire:model.live="search" placeholder="Поиск по названию..."
                   class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            
            <select wire:model.live="product_type_id"
                    class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="">Все типы</option>
                @foreach($types as $type)
                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                @endforeach
            </select>
        </div>

        <table class="table-auto w-full border">
            <thead>
            <tr class="bg-gray-100">
                <th class="px-2 py-1">ID</th>
                <th class="px-2 py-1">Тип продукта</th>
                <th class="px-2 py-1">Название</th>
                <th class="px-2 py-1">Цена/час (руб.)</th>
                <th class="px-2 py-1">Цена/шт (руб.)</th>
                <th class="px-2 py-1">Действия</th>
            </tr>
            </thead>
            <tbody>
            @forelse($models as $model)
                <tr>
                    <td class="border px-2 py-1">{{ $model->id }}</td>
                    <td class="border px-2 py-1">{{ $model->type->name }}</td>
                    <td class="border px-2 py-1">{{ $model->name }}</td>
                    <td class="border px-2 py-1">{{ $model->base_price_hour ?? '—' }}</td>
                    <td class="border px-2 py-1">{{ $model->base_price_each ?? '—' }}</td>
                    <td class="border px-2 py-1 text-right space-x-2">
                        <a href="{{ route('admin.product-models.edit', $model) }}" class="text-blue-600 hover:text-blue-800">Редактировать</a>
                        <button wire:click="delete({{ $model->id }})" 
                                wire:confirm="Удалить модель {{ $model->name }}?"
                                class="text-red-600 hover:text-red-800">
                            Удалить
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="border px-2 py-4 text-center text-gray-500">Модели не найдены</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $models->links() }}
        </div>
    </div>
</div>