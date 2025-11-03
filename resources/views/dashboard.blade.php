<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100">
    <!-- Навигация -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-xl font-bold text-gray-800">
                        🎱 Бильярд Клуб
                    </a>
                </div>

                <div class="flex items-center space-x-4">
                    <a href="{{ route('dashboard') }}" 
                       class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md bg-gray-100">
                        Главная
                    </a>

                    <!-- Выпадающее меню профиля -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" 
                                class="flex items-center space-x-2 text-gray-700 hover:text-gray-900 focus:outline-none">
                            <span class="font-medium">{{ auth()->user()->name }}</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="open" 
                             @click.away="open = false"
                             x-transition
                             class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10">
                            
                            <a href="{{ route('profile') }}" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                👤 Профиль
                            </a>

                            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('moderator'))
                            <a href="{{ route('admin.dashboard') }}" 
                               class="block px-4 py-2 text-sm text-blue-600 hover:bg-blue-50 font-semibold">
                                ⚙️ Админка
                            </a>
                            @endif

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" 
                                        class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                    🚪 Выйти
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Контент -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Добро пожаловать, {{ auth()->user()->name }}! 👋</h1>
            <p class="text-gray-600 mt-2">Здесь вы можете управлять своими бронированиями</p>
        </div>

        <!-- Быстрые действия -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
                <div class="text-4xl mb-4">📅</div>
                <h3 class="text-xl font-bold mb-2">Мои бронирования</h3>
                <p class="text-gray-600 mb-4">Просмотр активных и прошлых бронирований</p>
                <a href="#my-bookings" class="text-blue-600 hover:text-blue-800 font-medium">
                    Перейти →
                </a>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
                <div class="text-4xl mb-4">➕</div>
                <h3 class="text-xl font-bold mb-2">Новое бронирование</h3>
                <p class="text-gray-600 mb-4">Забронировать стол на нужное время</p>
                <span class="text-gray-400 text-sm">(Скоро доступно)</span>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
                <div class="text-4xl mb-4">👤</div>
                <h3 class="text-xl font-bold mb-2">Мой профиль</h3>
                <p class="text-gray-600 mb-4">Управление личными данными</p>
                <a href="{{ route('profile') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                    Перейти →
                </a>
            </div>
        </div>

        <!-- Мои бронирования -->
        <div id="my-bookings" class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold mb-6">Мои бронирования</h2>

            @php
                $userBookings = \App\Models\Booking::where('user_id', auth()->id())
                    ->with(['place', 'bookingResources.resource', 'order'])
                    ->orderBy('starts_at', 'desc')
                    ->limit(5)
                    ->get();
            @endphp

            @if($userBookings->count() > 0)
                <div class="space-y-4">
                    @foreach($userBookings as $booking)
                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-semibold text-lg">{{ $booking->place->name }}</h3>
                                <p class="text-gray-600">
                                    {{ \Carbon\Carbon::parse($booking->starts_at)->format('d.m.Y H:i') }} - 
                                    {{ \Carbon\Carbon::parse($booking->ends_at)->format('H:i') }}
                                </p>
                                <div class="mt-2">
                                    @foreach($booking->bookingResources as $br)
                                        <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">
                                            {{ $br->resource->code ?? 'Стол' }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-block px-3 py-1 text-sm font-semibold rounded
                                    @if($booking->status === 'confirmed') bg-green-100 text-green-800
                                    @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($booking->status === 'canceled') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $booking->status }}
                                </span>
                                @if($booking->order)
                                    <p class="text-lg font-bold text-green-600 mt-2">
                                        {{ number_format($booking->order->total_amount, 0, ',', ' ') }} ₽
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 text-gray-500">
                    <div class="text-6xl mb-4">📭</div>
                    <p class="text-xl">У вас пока нет бронирований</p>
                    <p class="mt-2">Создайте первое бронирование, чтобы начать играть!</p>
                </div>
            @endif
        </div>
    </main>
</body>
</html>