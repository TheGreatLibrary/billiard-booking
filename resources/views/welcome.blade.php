<!DOCTYPE html>
<html lang="ru" class="h-full dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Бронирование бильярдных столов | Бильярд Клуб</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen">
    <!-- Навигация -->
    <nav class="bg-gray-800 border-b border-gray-700 sticky top-0 z-50 backdrop-blur-sm bg-gray-800/95">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="flex items-center space-x-3 group">
                        <div class="w-10 h-10 bg-gradient-to-br from-amber-600 to-amber-800 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="text-amber-100 text-lg font-bold">🎱</span>
                        </div>
                        <span class="text-2xl font-bold text-amber-100">
                            Бильярд Клуб
                        </span>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('dashboard') }}" 
                           class="bg-gradient-to-r from-amber-700 to-amber-800 hover:from-amber-600 hover:to-amber-700 text-amber-100 px-6 py-2 rounded-lg font-medium transition-all duration-300 hover:shadow-lg border border-amber-600/30">
                            Личный кабинет
                        </a>
                    @else
                        <a href="{{ route('login') }}" 
                           class="text-gray-300 hover:text-amber-300 px-4 py-2 font-medium transition-colors border border-transparent hover:border-gray-600 rounded-lg">
                            Войти
                        </a>
                        <a href="{{ route('register') }}" 
                           class="bg-gradient-to-r from-amber-700 to-amber-800 hover:from-amber-600 hover:to-amber-700 text-amber-100 px-6 py-2 rounded-lg font-medium transition-all duration-300 hover:shadow-lg border border-amber-600/30">
                            Регистрация
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero секция -->
    <div class="relative overflow-hidden bg-gradient-to-br from-gray-800 to-gray-900">
        <!-- Текстура фона -->
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-gray-700/20 to-gray-900"></div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <div class="text-center">
                <!-- Бейдж -->
                <div class="inline-flex items-center gap-2 bg-gray-700/50 backdrop-blur-sm rounded-full px-4 py-2 mb-8 border border-gray-400">
                    <span class="w-2 h-2 bg-amber-500 rounded-full animate-pulse"></span>
                    <span class="text-sm font-medium text-gray-300">Онлайн бронирование 24/7</span>
                </div>
                
                <!-- Заголовок -->
                <h1 class="text-5xl md:text-6xl font-bold mb-6">
                    <span class="text-amber-100">Бронируйте</span>
                    <span class="block text-gray-300">бильярдные столы</span>
                </h1>
                
                <!-- Описание -->
                <p class="text-xl text-gray-400 mb-8 max-w-2xl mx-auto leading-relaxed">
                    Премиум бильярдный клуб с удобной системой онлайн-бронирования. 
                    Выбирайте время, оплачивайте онлайн, наслаждайтесь игрой.
                </p>
                
                <!-- Кнопки CTA -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-16">
                    @auth
                        <a href="{{ route('dashboard') }}" 
                           class="group bg-gradient-to-r from-amber-600 to-amber-700 hover:from-amber-500 hover:to-amber-600 text-amber-50 font-semibold px-8 py-4 rounded-xl text-lg transition-all duration-300 hover:scale-105 shadow-lg border border-amber-500/30 flex items-center gap-3">
                            <span>Перейти к бронированию</span>
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('register') }}" 
                           class="group bg-gradient-to-r from-amber-600 to-amber-700 hover:from-amber-500 hover:to-amber-600 text-amber-50 font-semibold px-8 py-4 rounded-xl text-lg transition-all duration-300 hover:scale-105 shadow-lg border border-amber-500/30 flex items-center gap-3">
                            <span>Начать бронирование</span>
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                        <a href="{{ route('login') }}" 
                           class="group border-2 border-gray-600 text-gray-300 hover:border-amber-500 hover:text-amber-300 font-semibold px-8 py-4 rounded-xl text-lg transition-all duration-300 hover:scale-105 flex items-center gap-3 backdrop-blur-sm">
                            <span>Уже есть аккаунт?</span>
                        </a>
                    @endauth
                </div>

                <!-- Статистика -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 max-w-2xl mx-auto">
                    <div class="text-center p-4 bg-gray-800/50 rounded-lg backdrop-blur-sm border border-gray-700">
                        <div class="text-2xl font-bold text-amber-400">500+</div>
                        <div class="text-gray-400 text-sm">Постоянных клиентов</div>
                    </div>
                    <div class="text-center p-4 bg-gray-800/50 rounded-lg backdrop-blur-sm border border-gray-700">
                        <div class="text-2xl font-bold text-amber-400">24/7</div>
                        <div class="text-gray-400 text-sm">Бронирование</div>
                    </div>
                    <div class="text-center p-4 bg-gray-800/50 rounded-lg backdrop-blur-sm border border-gray-700">
                        <div class="text-2xl font-bold text-amber-400">12</div>
                        <div class="text-gray-400 text-sm">Профессиональных столов</div>
                    </div>
                    <div class="text-center p-4 bg-gray-800/50 rounded-lg backdrop-blur-sm border border-gray-700">
                        <div class="text-2xl font-bold text-amber-400">98%</div>
                        <div class="text-gray-400 text-sm">Положительных отзывов</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Преимущества -->
    <div class="bg-gray-800 py-20 border-t border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-amber-100 mb-4">Почему выбирают нас</h2>
                <p class="text-xl text-gray-400 max-w-2xl mx-auto">
                    Элитный бильярдный клуб с безупречным сервисом
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Карточка 1 -->
                <div class="bg-gray-700/50 backdrop-blur-sm rounded-xl p-8 border border-gray-600 hover:border-amber-500/30 transition-all duration-300 hover:scale-105 group">
                    <h3 class="text-xl font-bold text-amber-100 mb-4">Профессиональные столы</h3>
                    <p class="text-gray-300 leading-relaxed">
                        Только профессиональное оборудование для настоящих ценителей бильярда
                    </p>
                </div>
                
                <!-- Карточка 2 -->
                <div class="bg-gray-700/50 backdrop-blur-sm rounded-xl p-8 border border-gray-600 hover:border-amber-500/30 transition-all duration-300 hover:scale-105 group">
                    <h3 class="text-xl font-bold text-amber-100 mb-4">Быстрое бронирование</h3>
                    <p class="text-gray-300 leading-relaxed">
                        Забронируйте стол за 2 минуты без звонков и ожидания
                    </p>
                </div>
                
                <!-- Карточка 3 -->
                <div class="bg-gray-700/50 backdrop-blur-sm rounded-xl p-8 border border-gray-600 hover:border-amber-500/30 transition-all duration-300 hover:scale-105 group">
                    <h3 class="text-xl font-bold text-amber-100 mb-4">Безопасная оплата</h3>
                    <p class="text-gray-300 leading-relaxed">
                        Защищённая онлайн-оплата с мгновенным подтверждением брони
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA секция -->
    <div class="bg-gradient-to-br from-gray-800 to-gray-900 py-20 border-t border-gray-700">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-bold text-amber-100 mb-6">
                Готовы к игре?
            </h2>
            <p class="text-xl text-gray-400 mb-8 max-w-2xl mx-auto">
                Присоединяйтесь к сообществу настоящих ценителей бильярда
            </p>
            
            @auth
                <a href="{{ route('dashboard') }}" 
                   class="inline-flex items-center gap-3 bg-amber-600 hover:bg-amber-700 text-amber-50 font-semibold px-8 py-4 rounded-xl text-lg transition-all duration-300 hover:scale-105 border border-amber-500/30">
                    <span>Перейти в кабинет</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            @else
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('register') }}" 
                       class="inline-flex items-center gap-3 bg-amber-600 hover:bg-amber-700 text-amber-50 font-semibold px-8 py-4 rounded-xl text-lg transition-all duration-300 hover:scale-105 border border-amber-500/30">
                        <span>Создать аккаунт</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                    <a href="{{ route('login') }}" 
                       class="inline-flex items-center gap-3 border-2 border-gray-600 text-gray-300 hover:border-amber-500 hover:text-amber-300 font-semibold px-8 py-4 rounded-xl text-lg transition-all duration-300 hover:scale-105 backdrop-blur-sm">
                        <span>Войти в аккаунт</span>
                    </a>
                </div>
            @endauth
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 py-12 border-t border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Лого и описание -->
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-amber-600 to-amber-800 rounded-lg flex items-center justify-center">
                            <span class="text-amber-100 text-lg font-bold">🎱</span>
                        </div>
                        <span class="text-2xl font-bold text-amber-100">Бильярд Клуб</span>
                    </div>
                    <p class="text-gray-400 max-w-md">
                        Премиум бильярдный клуб с безупречным сервисом и профессиональным оборудованием. 
                        Играйте с комфортом и стилем.
                    </p>
                </div>
                
                <!-- Навигация -->
                <div>
                    <h4 class="text-amber-200 font-semibold mb-4">Навигация</h4>
                    <ul class="space-y-2">
                        <li><a href="/" class="text-gray-400 hover:text-amber-300 transition-colors">Главная</a></li>
                        @auth
                            <li><a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-amber-300 transition-colors">Личный кабинет</a></li>
                        @else
                            <li><a href="{{ route('login') }}" class="text-gray-400 hover:text-amber-300 transition-colors">Войти</a></li>
                            <li><a href="{{ route('register') }}" class="text-gray-400 hover:text-amber-300 transition-colors">Регистрация</a></li>
                        @endauth
                    </ul>
                </div>
                
                <!-- Контакты -->
                <div>
                    <h4 class="text-amber-200 font-semibold mb-4">Контакты</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li class="flex items-center gap-2">
                            <span class="text-amber-400">📞</span>
                            +7 (XXX) XXX-XX-XX
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-amber-400">✉️</span>
                            info@billiard-club.ru
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-amber-400">📍</span>
                            г. Москва, ул. Примерная, 123
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Копирайт -->
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-500">
                <p>&copy; 2024 Бильярд Клуб. Все права защищены.</p>
            </div>
        </div>
    </footer>
</body>
</html>