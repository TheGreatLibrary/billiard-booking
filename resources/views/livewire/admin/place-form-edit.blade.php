<div class="space-y-6">
    <h1 class="mb-4 text-xl font-bold">Редактировать локацию</h1>
    <form wire:submit.prevent="save" class="space-y-4 max-w-2xl">
        <div class="flex items-center">
            <label class="form-label w-32 mb-0">Название</label>
            <input 
                type="text" 
                wire:model="name" 
                class="form-control flex-1 @error('name') is-invalid @enderror" 
                required
            >
            @error('name')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
        <div class="flex items-center">
            <label class="form-label w-32 mb-0">Адрес</label>
            <input 
                type="text" 
                wire:model="address" 
                class="form-control flex-1 @error('address') is-invalid @enderror" 
                required
            >
            @error('address')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
        <div class="flex items-start">
            <label class="form-label w-32 mb-0 pt-2">Описание</label>
            <textarea 
                wire:model="description" 
                class="form-control flex-1 @error('description') is-invalid @enderror" 
                rows="4"
            ></textarea>
            @error('description')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
        <div class="mt-4">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Обновить</button>
            <a href="{{ route('admin.places.index') }}" class="px-4 py-2 bg-gray-400 text-white rounded inline-block ml-2">Отмена</a>
        </div>
    </form>
</div>