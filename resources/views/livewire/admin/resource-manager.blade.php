<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Управление ресурсами</h1>

    @if (session('success'))
        <div class="bg-green-100 text-green-800 p-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Форма добавления/редактирования -->
    <form wire:submit.prevent="save" class="bg-white shadow rounded p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium">Модель</label>
                <select wire:model="model_id" class="w-full border rounded px-3 py-2">
                    <option value="">Выберите модель</option>
                    @foreach($models as $model)
                        <option value="{{ $model->id }}">{{ $model->name }}</option>
                    @endforeach
                </select>
                @error('model_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Зона</label>
                <select wire:model="zone_id" class="w-full border rounded px-3 py-2">
                    <option value="">Выберите зону</option>
                    @foreach($zones as $zone)
                        <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                    @endforeach
                </select>
                @error('zone_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Состояние</label>
                <select wire:model="state_id" class="w-full border rounded px-3 py-2">
                    <option value="">Выберите состояние</option>
                    @foreach($states as $state)
                        <option value="{{ $state->id }}">{{ $state->name }}</option>
                    @endforeach
                </select>
                @error('state_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Код</label>
                <input wire:model="code" type="text" class="w-full border rounded px-3 py-2">
                @error('code') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="mt-4">
            <label class="block text-sm font-medium">Примечание</label>
            <textarea wire:model="note" class="w-full border rounded px-3 py-2"></textarea>
        </div>

        <div class="mt-4 flex space-x-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                {{ $editingResourceId ? 'Обновить' : 'Добавить' }}
            </button>
            <button type="button" wire:click="resetForm" class="bg-gray-400 text-white px-4 py-2 rounded">
                Очистить
            </button>
        </div>
    </form>

    <!-- Таблица -->
    <table class="min-w-full bg-white border rounded shadow">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 text-left">ID</th>
                <th class="px-4 py-2 text-left">Модель</th>
                <th class="px-4 py-2 text-left">Зона</th>
                <th class="px-4 py-2 text-left">Состояние</th>
                <th class="px-4 py-2 text-left">Код</th>
                <th class="px-4 py-2 text-left">Действия</th>
            </tr>
        </thead>
        <tbody>
            @foreach($resources as $resource)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $resource->id }}</td>
                    <td class="px-4 py-2">{{ $resource->model->name ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $resource->zone->name ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $resource->state->name ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $resource->code ?? '-' }}</td>
                    <td class="px-4 py-2 flex space-x-2">
                        <button wire:click="edit({{ $resource->id }})" class="bg-yellow-500 text-white px-3 py-1 rounded">Изм.</button>
                        <button wire:click="delete({{ $resource->id }})" class="bg-red-600 text-white px-3 py-1 rounded">X</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">{{ $resources->links() }}</div>
</div>
