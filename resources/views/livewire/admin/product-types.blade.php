<div class="p-4">
    <h1 class="text-2xl font-bold mb-4">Типы продуктов</h1>

    @if (session()->has('success'))
        <div class="bg-green-100 text-green-800 p-2 mb-4 rounded">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit.prevent="save" class="mb-4">
        <div class="flex items-center space-x-2">
            <input type="text" wire:model="name" placeholder="Название типа" class="border p-2 rounded w-1/3">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                {{ $isEdit ? 'Обновить' : 'Добавить' }}
            </button>
            @if($isEdit)
                <button type="button" wire:click="resetForm" class="bg-gray-400 text-white px-4 py-2 rounded">
                    Отмена
                </button>
            @endif
        </div>
        @error('name') <span class="text-red-500">{{ $message }}</span> @enderror
    </form>

    <table class="table-auto w-full border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-100">
                <th class="border px-4 py-2">ID</th>
                <th class="border px-4 py-2">Название</th>
                <th class="border px-4 py-2">Действия</th>
            </tr>
        </thead>
        <tbody>
            @foreach($types as $type)
                <tr>
                    <td class="border px-4 py-2">{{ $type->id }}</td>
                    <td class="border px-4 py-2">{{ $type->name }}</td>
                    <td class="border px-4 py-2">
                        <button wire:click="edit({{ $type->id }})" class="bg-yellow-500 text-white px-2 py-1 rounded">Изменить</button>
                        <button wire:click="delete({{ $type->id }})" class="bg-red-600 text-white px-2 py-1 rounded">Удалить</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $types->links() }}
    </div>
</div>
