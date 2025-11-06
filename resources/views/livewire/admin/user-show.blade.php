<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold mb-4">Профиль пользователя #{{ $user->id }}</h2>

        <div class="space-y-4">
            <div>
                <span class="font-semibold text-gray-700">Имя:</span>
                <p class="text-gray-900">{{ $user->name }}</p>
            </div>

            <div>
                <span class="font-semibold text-gray-700">Email:</span>
                <p class="text-gray-900">{{ $user->email }}</p>
            </div>

            <div>
                <span class="font-semibold text-gray-700">Телефон:</span>
                <p class="text-gray-900">{{ $user->phone ?? '—' }}</p>
            </div>

            <div>
                <span class="font-semibold text-gray-700">Роли:</span>
                <div class="mt-1">
                    @foreach($user->roles as $role)
                        <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mr-1">
                            {{ $role->name }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-between">
            <a href="{{ route('admin.users.index') }}" 
               class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg">
                ← Назад
            </a>
            <a href="{{ route('admin.users.edit', $user->id) }}" 
               class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                Редактировать
            </a>
        </div>
    </div>
</div>
