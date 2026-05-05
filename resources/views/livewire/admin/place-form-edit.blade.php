<div class="max-w-2xl mx-auto p-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Редактировать: {{ $place->name }}</h1>

    <form wire:submit.prevent="save" class="space-y-6 bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 p-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Название</label>
            <input type="text" wire:model="name" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
            @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Адрес</label>
            <input type="text" wire:model="address" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
            @error('address') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Описание</label>
            <textarea wire:model="description" rows="3" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-blue-500 focus:border-blue-500"></textarea>
            @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Фоновое изображение зала</label>
            <div class="mt-1">
                @if($hall_image)
                    {{-- Новое загруженное изображение --}}
                    <div class="relative inline-block mb-3">
                        <img src="{{ $hall_image->temporaryUrl() }}" class="max-h-40 rounded-lg border border-gray-200">
                        <button type="button" wire:click="removeImage" class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-600">✕</button>
                    </div>
                @elseif($existing_image)
                    {{-- Текущее изображение --}}
                    <div class="relative inline-block mb-3">
                        <img src="{{ asset('storage/' . $existing_image) }}" class="max-h-40 rounded-lg border border-gray-200">
                        <button type="button" wire:click="removeImage" class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-600">✕</button>
                    </div>
                @endif

                <label class="flex flex-col items-center justify-center p-6 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl cursor-pointer hover:border-blue-500 transition">
                    <input type="file" wire:model="hall_image" accept="image/*" class="hidden">
                    <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <p class="text-sm text-gray-500">{{ $existing_image || $hall_image ? 'Заменить изображение' : 'Загрузить изображение' }}</p>
                </label>
            </div>
            <div wire:loading wire:target="hall_image" class="mt-2 text-sm text-blue-600">Загрузка...</div>
            @error('hall_image') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">Сохранить</button>
            <a href="{{ route('admin.places.index') }}" class="px-6 py-2.5 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition">Отмена</a>
        </div>
    </form>
</div>