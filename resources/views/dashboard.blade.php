<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет</title>
     @vite(['resources/css/app.css', 'resources/js/app.js'])
   <!-- <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script> -->
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

                            @if(auth()->user()->hasRole('admin'))
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

        
    </main>
</body>
</html>