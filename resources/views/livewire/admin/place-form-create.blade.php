<div class="space-y-6">
    <div class="container">
        <h1>Добавить локацию</h1>

        <form wire:submit.prevent="save">
            <div class="mb-3">
                <label class="form-label">Название</label>
                <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror" required>
                @error('name')<div class="text-danger">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Адрес</label>
                <input type="text" wire:model="address" class="form-control @error('address') is-invalid @enderror" required>
                @error('address')<div class="text-danger">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Описание</label>
                <textarea wire:model="description" class="form-control @error('description') is-invalid @enderror" rows="4"></textarea>
                @error('description')<div class="text-danger">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="btn btn-success">Сохранить</button>
            <a href="{{ route('admin.places.list') }}" class="btn btn-secondary">Отмена</a>
        </form>
    </div>
</div>