<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Бронирование бильярдных столов</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white">
    <!-- Навигация -->
    <nav class="bg-gray-800 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-2xl font-bold">🎱 Бильярд Клуб</span>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="bg-green-500 hover:bg-green-600 px-4 py-2 rounded-lg">
                            Личный кабинет
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-300 hover:text-white px-3 py-2">
                            Войти
                        </a>
                        <a href="{{ route('register') }}" class="bg-blue-500 hover:bg-blue-600 px-4 py-2 rounded-lg">
                            Регистрация
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero секция -->
    <div class="relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <div class="text-center">
                <h1 class="text-5xl font-extrabold mb-6">
                    Бронируйте столы онлайн
                </h1>
                <p class="text-xl text-gray-300 mb-8">
                    Удобная система бронирования бильярдных столов. Выбирайте время, оплачивайте онлайн.
                </p>
                @auth
                    <a href="{{ route('dashboard') }}" 
                       class="inline-block bg-green-500 hover:bg-green-600 text-white font-bold px-8 py-4 rounded-lg text-lg">
                        Перейти к бронированию →
                    </a>
                @else
                    <a href="{{ route('register') }}" 
                       class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-bold px-8 py-4 rounded-lg text-lg">
                        Начать бронирование →
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Преимущества -->
    <div class="bg-gray-800 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="text-4xl mb-4">⚡</div>
                    <h3 class="text-xl font-bold mb-2">Быстрое бронирование</h3>
                    <p class="text-gray-400">Забронируйте стол за пару минут</p>
                </div>
                <div class="text-center">
                    <div class="text-4xl mb-4">💳</div>
                    <h3 class="text-xl font-bold mb-2">Онлайн оплата</h3>
                    <p class="text-gray-400">Безопасная оплата картой</p>
                </div>
                <div class="text-center">
                    <div class="text-4xl mb-4">📱</div>
                    <h3 class="text-xl font-bold mb-2">Личный кабинет</h3>
                    <p class="text-gray-400">История всех ваших бронирований</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 py-8 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-gray-400">
            <p>&copy; 2025 Бильярд Клуб. Все права защищены.</p>
        </div>
    </footer>
</body>
</html>