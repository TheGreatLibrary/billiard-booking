<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold mb-6">
            {{ $userId ? 'Редактирование пользователя' : 'Создание пользователя' }}
        </h2>

        <form wire:submit.prevent="save" class="space-y-5">
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Имя</label>
                <input type="text" wire:model="name" class="w-full border rounded px-3 py-2" placeholder="Введите имя">
                @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-semibold text-gray-700 mb-1">Email</label>
                <input type="email" wire:model="email" class="w-full border rounded px-3 py-2" placeholder="Введите email">
                @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-semibold text-gray-700 mb-1">Телефон</label>
                <input type="text" wire:model="phone" class="w-full border rounded px-3 py-2" placeholder="Введите телефон">
                @error('phone') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-semibold text-gray-700 mb-1">Пароль</label>
                <input type="password" wire:model="password" class="w-full border rounded px-3 py-2" placeholder="Новый пароль (необязательно)">
                @error('password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-semibold text-gray-700 mb-1">Роли</label>
                <div class="space-y-2">
                    @foreach($availableRoles as $role)
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" wire:model="roles" value="{{ $role->id }}">
                            <span>{{ $role->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-between mt-6">
                <a href="{{ route('admin.users.index') }}" 
                   class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg">
                    Отмена
                </a>
                <button type="submit" 
                        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg">
                    Сохранить
                </button>
            </div>
        </form>
    </div>
</div>
