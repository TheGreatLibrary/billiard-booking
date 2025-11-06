<div class="space-y-6 max-w-xl mx-auto">
    <h1 class="text-2xl font-semibold text-gray-800">
        {{ $isEdit ? 'Редактировать тип продукта' : 'Добавить тип продукта' }}
    </h1>

    <form wire:submit.prevent="save" class="space-y-4 bg-white p-6 shadow rounded-lg">
        <div>
            <label class="block text-sm font-medium text-gray-700">Название</label>
            <input type="text" wire:model="name"
                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="flex justify-end space-x-2">
            <a href="{{ route('admin.product-types.index') }}"
               class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-md">
                Назад
            </a>
            <button type="submit"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                {{ $isEdit ? 'Сохранить' : 'Создать' }}
            </button>
        </div>
    </form>
</div>
