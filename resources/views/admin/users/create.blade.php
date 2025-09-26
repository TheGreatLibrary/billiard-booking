@extends('admin.layout.app')

@section('title', 'Добавить пользователя')

@section('content')
<h1 class="text-2xl font-bold mb-6">➕ Добавить пользователя</h1>

<form action="{{ route('admin.users.store') }}" method="POST" class="bg-white p-6 rounded shadow w-full max-w-lg">
    @csrf

    <div class="mb-4">
        <label class="block mb-1 font-medium">Имя</label>
        <input type="text" name="name" value="{{ old('name') }}" class="w-full border p-2 rounded" required>
        @error('name')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
    </div>

    <div class="mb-4">
        <label class="block mb-1 font-medium">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" class="w-full border p-2 rounded" required>
        @error('email')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
    </div>

    <div class="mb-4">
        <label class="block mb-1 font-medium">Телефон</label>
        <input type="text" name="phone" value="{{ old('phone') }}" class="w-full border p-2 rounded" required>
        @error('phone')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
    </div>

    <div class="mb-4">
        <label class="block mb-1 font-medium">Пароль</label>
        <input type="password" name="password" class="w-full border p-2 rounded" required>
        @error('password')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
    </div>

    <div class="mb-4">
        <label class="block mb-1 font-medium">Роли</label>
        <div class="space-y-1">
            @foreach($roles as $role)
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="roles[]" value="{{ $role->id }}">
                    <span>{{ $role->name }}</span>
                </label>
            @endforeach
        </div>
    </div>

    <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Создать</button>
    <a href="{{ route('admin.users.index') }}" class="ml-2 text-gray-600 hover:underline">Отмена</a>
</form>
@endsection
