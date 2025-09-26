@extends('admin.layout.app')

@section('title', 'Профиль пользователя')

@section('content')
<div class="bg-white p-6 rounded shadow w-full max-w-lg">
    <h1 class="text-2xl font-bold mb-4">👤 {{ $user->name }}</h1>

    <p><strong>Email:</strong> {{ $user->email }}</p>
    <p><strong>Телефон:</strong> {{ $user->phone }}</p>
    <p class="mt-2"><strong>Роли:</strong>
        @foreach($user->roles as $role)
            <span class="bg-gray-100 px-2 py-1 rounded text-xs">{{ $role->name }}</span>
        @endforeach
    </p>

    <div class="mt-6 flex space-x-3">
        <a href="{{ route('admin.users.edit', $user) }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">✏️ Редактировать</a>
        <a href="{{ route('admin.users.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">⬅ Назад</a>
    </div>
</div>
@endsection
