@extends('admin.layout.app')

@section('title', 'Панель управления')

@section('content')
<!-- Заголовок и быстрые действия -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Панель управления</h1>
        <p class="text-gray-600">Обзор системы бронирования бильярда</p>
    </div>
    <div class="flex space-x-3">
        <a href="{{ route('admin.bookings.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center">
            ➕ Новое бронирование
        </a>
        <a href="{{ route('admin.users.create') }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg flex items-center">
            👥 Добавить пользователя
        </a>
    </div>
</div>

<!-- Статистика в виде карточек -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Пользователи -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600">Пользователи</p>
                <p class="text-2xl font-bold">{{ $stats['total_users'] }}</p>
                <p class="text-sm text-green-600">+{{ $stats['new_users_today'] }} сегодня</p>
            </div>
            <div class="text-3xl text-blue-500">👥</div>
        </div>
    </div>

    <!-- Бронирования -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600">Бронирования</p>
                <p class="text-2xl font-bold">{{ $stats['total_bookings'] }}</p>
                <div class="flex space-x-2 text-xs">
                    <span class="text-green-600">✓ {{ $stats['active_bookings'] }}</span>
                    <span class="text-yellow-600">⏳ {{ $stats['pending_bookings'] }}</span>
                </div>
            </div>
            <div class="text-3xl text-green-500">📅</div>
        </div>
    </div>

    <!-- Выручка -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600">Выручка</p>
                <p class="text-2xl font-bold">{{ number_format($stats['total_revenue'], 0) }} ₽</p>
                <p class="text-sm text-purple-600">{{ number_format($stats['today_revenue'], 0) }} ₽ сегодня</p>
            </div>
            <div class="text-3xl text-purple-500">💳</div>
        </div>
    </div>

    <!-- Столы -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600">Столы</p>
                <p class="text-2xl font-bold">{{ $stats['total_places'] }}</p>
                <p class="text-sm text-blue-600">✓ {{ $stats['available_places'] }} доступно</p>
            </div>
            <div class="text-3xl text-red-500">🎱</div>
        </div>
    </div>
</div>

<!-- Две колонки: последние бронирования и пользователи -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Последние бронирования -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b">
            <h2 class="text-lg font-semibold flex items-center">
                📅 Последние бронирования
                <a href="{{ route('admin.bookings.index') }}" class="text-sm text-blue-500 ml-auto hover:underline">Все бронирования →</a>
            </h2>
        </div>
        <div class="p-6">
            @if($recentBookings->count() > 0)
            <div class="space-y-4">
                @foreach($recentBookings as $booking)
                <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <span class="text-blue-600">🎱</span>
                        </div>
                        <div>
                            <p class="font-medium">{{ $booking->user->name ?? 'Гость' }}</p>
                            <p class="text-sm text-gray-600">{{ $booking->place->name ?? 'Стол' }}</p>
                            <p class="text-xs text-gray-500">{{ $booking->start_time->format('d.m.Y H:i') }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="px-2 py-1 rounded text-xs 
                            {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $booking->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $booking->status === 'canceled' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ $booking->status }}
                        </span>
                        <a href="{{ route('admin.bookings.show', $booking) }}" class="text-blue-500 hover:text-blue-700 text-sm block mt-1">
                            Просмотр →
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-500 text-center py-4">Бронирования не найдены</p>
            @endif
        </div>
    </div>

    <!-- Последние пользователи -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b">
            <h2 class="text-lg font-semibold flex items-center">
                👥 Новые пользователи
                <a href="{{ route('admin.users.index') }}" class="text-sm text-blue-500 ml-auto hover:underline">Все пользователи →</a>
            </h2>
        </div>
        <div class="p-6">
            @if($recentUsers->count() > 0)
            <div class="space-y-4">
                @foreach($recentUsers as $user)
                <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                            <span class="text-green-600">👤</span>
                        </div>
                        <div>
                            <p class="font-medium">{{ $user->name }}</p>
                            <p class="text-sm text-gray-600">{{ $user->email }}</p>
                            <p class="text-xs text-gray-500">Зарегистрирован: {{ $user->created_at->format('d.m.Y') }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        @foreach($user->roles as $role)
                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">{{ $role->name }}</span>
                        @endforeach
                        <a href="{{ route('admin.users.show', $user) }}" class="text-blue-500 hover:text-blue-700 text-sm block mt-1">
                            Профиль →
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-500 text-center py-4">Пользователи не найдены</p>
            @endif
        </div>
    </div>
</div>

<!-- График статистики за неделю -->
<div class="bg-white rounded-lg shadow-md p-6 mb-8">
    <h2 class="text-lg font-semibold mb-4">📈 Статистика за последние 7 дней</h2>
    <div class="grid grid-cols-7 gap-2 text-center">
        @foreach($weeklyStats as $day)
        <div>
            <p class="text-xs text-gray-600">{{ $day['date'] }}</p>
            <div class="mt-2 space-y-1">
                <div class="text-blue-500 text-sm">👥 {{ $day['users'] }}</div>
                <div class="text-green-500 text-sm">📅 {{ $day['bookings'] }}</div>
                <div class="text-purple-500 text-sm">💰 {{ $day['revenue'] }}₽</div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Быстрые действия -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <a href="{{ route('admin.bookings.index') }}" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition duration-200 text-center">
        <div class="text-3xl mb-2">📅</div>
        <h3 class="font-semibold">Управление бронированиями</h3>
        <p class="text-sm text-gray-600 mt-2">Просмотр и редактирование всех бронирований</p>
    </a>

    <a href="{{ route('admin.users.index') }}" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition duration-200 text-center">
        <div class="text-3xl mb-2">👥</div>
        <h3 class="font-semibold">Управление пользователями</h3>
        <p class="text-sm text-gray-600 mt-2">Создание и редактирование пользователей</p>
    </a>

    <a href="{{ route('admin.orders.index') }}" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition duration-200 text-center">
        <div class="text-3xl mb-2">🛒</div>
        <h3 class="font-semibold">Заказы и платежи</h3>
        <p class="text-sm text-gray-600 mt-2">Просмотр заказов и финансовых операций</p>
    </a>
</div>
@endsection