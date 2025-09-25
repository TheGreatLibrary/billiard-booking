<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои бронирования - Бильярд</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <h1 class="text-xl font-bold">Бильярд Клуб</h1>
                <div class="flex items-center space-x-4">
                    @auth
                        <span>Привет, {{ auth()->user()->name }}!</span>
                        <a href="{{ route('dashboard') }}" class="hover:underline">Панель управления</a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="hover:underline">Выйти</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="hover:underline">Войти</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Мои бронирования</h2>
            <a href="{{ route('bookings.create') }}" 
               class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition duration-200">
                ➕ Новое бронирование
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if($bookings->count() > 0)
            <div class="grid gap-6">
                @foreach($bookings as $booking)
                <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition duration-200">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">
                                Стол: {{ $booking->place->name ?? 'Не указан' }}
                            </h3>
                            <p class="text-gray-600 mt-2">
                                🕒 {{ $booking->start_time->format('d.m.Y H:i') }} - 
                                {{ $booking->end_time->format('H:i') }}
                            </p>
                            <p class="text-gray-600">
                                Статус: 
                                <span class="font-medium 
                                    @if($booking->status === 'confirmed') text-green-600
                                    @elseif($booking->status === 'pending') text-yellow-600
                                    @elseif($booking->status === 'canceled') text-red-600
                                    @endif">
                                    {{ $booking->status }}
                                </span>
                            </p>
                            @if($booking->notes)
                                <p class="text-gray-600 mt-2">💬 {{ $booking->notes }}</p>
                            @endif
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('bookings.show', $booking) }}" 
                               class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm">
                                👁️ Просмотр
                            </a>
                            @if($booking->status === 'pending')
                            <a href="{{ route('bookings.edit', $booking) }}" 
                               class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm">
                                ✏️ Изменить
                            </a>
                            <form action="{{ route('bookings.destroy', $booking) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm"
                                        onclick="return confirm('Отменить бронирование?')">
                                    ❌ Отменить
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-lg shadow-md p-8 text-center">
                <div class="text-6xl mb-4">🎱</div>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">У вас пока нет бронирований</h3>
                <p class="text-gray-500 mb-4">Забронируйте стол для игры в бильярд</p>
                <a href="{{ route('bookings.create') }}" 
                   class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg inline-block">
                    Создать первое бронирование
                </a>
            </div>
        @endif
    </div>
</body>
</html>