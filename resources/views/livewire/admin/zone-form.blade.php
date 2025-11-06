<div class="max-w-2xl mx-auto bg-white shadow rounded-lg p-6 space-y-6">
    <form wire:submit.prevent="save" class="space-y-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Заведение</label>
            <select wire:model="place_id"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="">Выберите заведение</option>
                @foreach($places as $place)
                    <option value="{{ $place->id }}">{{ $place->name }}</option>
                @endforeach
            </select>
            @error('place_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Название зоны</label>
            <input type="text" wire:model="name"
                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Коэффициент цены</label>
            <input type="number" step="0.001" wire:model="price_coef"
                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            @error('price_coef') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('admin.zones.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-md">
                Отмена
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white hover:bg-blue-700 rounded-md">
                {{ $zoneId ? 'Обновить' : 'Создать' }}
            </button>
        </div>
    </form>
</div>
