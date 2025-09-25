@extends('admin.layout.app')

@section('title', 'Управление пользователями')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Пользователи</h1>
    <a href="{{ route('admin.users.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
        ➕ Добавить пользователя
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="p-6">
        @if($users->count() > 0)
        <table class="w-full">
            <thead>
                <tr class="border-b">
                    <th class="text-left p-3">ID</th>
                    <th class="text-left p-3">Имя</th>
                    <th class="text-left p-3">Email</th>
                    <th class="text-left p-3">Телефон</th>
                    <th class="text-left p-3">Роли</th>
                    <th class="text-left p-3">Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3">{{ $user->id }}</td>
                    <td class="p-3">{{ $user->name }}</td>
                    <td class="p-3">{{ $user->email }}</td>
                    <td class="p-3">{{ $user->phone }}</td>
                    <td class="p-3">
                        @foreach($user->roles as $role)
                        <span class="bg-gray-100 px-2 py-1 rounded text-xs">{{ $role->name }}</span>
                        @endforeach
                    </td>
                    <td class="p-3">
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.users.show', $user) }}" class="text-blue-500 hover:text-blue-700">👁️</a>
                            <a href="{{ route('admin.users.edit', $user) }}" class="text-green-500 hover:text-green-700">✏️</a>
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('Удалить пользователя?')">🗑️</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="mt-4">
            {{ $users->links() }}
        </div>
        @else
        <div class="text-center py-8">
            <p class="text-gray-500">Пользователи не найдены</p>
            <a href="{{ route('admin.users.create') }}" class="text-blue-500 hover:underline">Добавить первого пользователя</a>
        </div>
        @endif
    </div>
</div>
@endsection