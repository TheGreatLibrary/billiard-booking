@extends('layouts.app')

@section('title', 'Мой профиль')

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Мой профиль</h1>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Личные данные -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Личные данные</h2>
            
            <form action="{{ route('user.profile.update') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Имя *</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Телефон *</label>
                        <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                        @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                        @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" 
                            class="w-full bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium">
                        💾 Сохранить изменения
                    </button>
                </div>
            </form>
        </div>

        <!-- Изменение пароля -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Изменить пароль</h2>
            
            <form action="{{ route('user.profile.password') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Текущий пароль *</label>
                        <input type="password" name="current_password" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                        @error('current_password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Новый пароль *</label>
                        <input type="password" name="password" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                        @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Подтверждение пароля *</label>
                        <input type="password" name="password_confirmation" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                    </div>

                    <button type="submit" 
                            class="w-full bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg font-medium">
                        🔒 Изменить пароль
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Информация об аккаунте -->
    <div class="bg-white rounded-lg shadow-md p-6 mt-6">
        <h2 class="text-xl font-semibold mb-4">Информация об аккаунте</h2>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-gray-500">Дата регистрации</p>
                <p class="font-medium">{{ $user->created_at->format('d.m.Y H:i') }}</p>
            </div>
            <div>
                <p class="text-gray-500">Роль</p>
                <p class="font-medium">
                    @if($user->hasRole('admin'))
                        <span class="text-blue-600">Администратор</span>
                    @else
                        Пользователь
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>
@endsection