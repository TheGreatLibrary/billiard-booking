<div>
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Пользователи</h1>
            <p class="text-gray-600">Список всех пользователей системы</p>
        </div>
        <a href="{{ route('admin.users.create') }}" 
           class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
            Новый пользователь
        </a>
    </div>

    <!-- Фильтр -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <input type="text" 
               wire:model.live="search" 
               placeholder="Поиск по имени или email..."
               class="w-full border rounded px-3 py-2">
    </div>

    <!-- Таблица -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        @if($users->count() > 0)
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Имя</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Телефон</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Роли</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Действия</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">#{{ $user->id }}</td>
                        <td class="px-6 py-4 font-medium text-gray-800">{{ $user->name }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $user->email }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $user->phone ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            @foreach($user->roles as $role)
                                <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mr-1">
                                    {{ $role->name }}
                                </span>
                            @endforeach
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.users.show', $user->id) }}" 
                                   class="text-blue-600 hover:text-blue-900">Просмотр</a>
                                <a href="{{ route('admin.users.edit', $user->id) }}" 
                                   class="text-green-600 hover:text-green-900">Изменить</a>
                                <button wire:click="delete({{ $user->id }})" 
                                        wire:confirm="Удалить пользователя?"
                                        class="text-red-600 hover:text-red-900">
                                    Удалить
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="p-4">
                {{ $users->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <h3 class="text-lg font-medium mb-2">Пользователи не найдены</h3>
                <a href="{{ route('admin.users.create') }}" 
                   class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                    Добавить пользователя
                </a>
            </div>
        @endif
    </div>
</div>
