<!DOCTYPE html>
<html lang="ru" x-data="themeData()" x-init="initTheme()" :class="{ 'dark': isDark }" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Личный кабинет</title>

    <!-- Скрываем контент до инициализации темы -->
    <style>
        [x-cloak] {
            display: none !important;
        }
        
        .theme-loading {
            visibility: hidden;
        }
        
        .theme-loaded {
            visibility: visible;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200 transition-colors duration-300 theme-loading"
      x-bind:class="isDark ? 'theme-loaded' : 'theme-loaded'"
      x-cloak>

    <!-- Навигация -->
    <nav class="bg-white dark:bg-gray-800 shadow-lg transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">

                 <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center space-x-3 group">
                        <div class="w-10 h-10 bg-gradient-to-br from-amber-600 to-amber-800 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="text-amber-100 text-lg font-bold">🎱</span>
                        </div>
                        <span class="text-2xl font-bold text-amber-100">
                            Бильярд Клуб
                        </span>
                    </a>
                </div>

                <div class="flex items-center space-x-4">

                    <a href="{{ route('dashboard') }}" 
                       class="px-3 py-2 rounded-md text-sm font-medium
                              bg-gray-100 dark:bg-gray-700
                              text-gray-700 dark:text-gray-200 hover:text-gray-900 dark:hover:text-white">
                        Главная
                    </a>

                    <!-- Кнопка темной темы -->
                    <button @click="toggleTheme()" 
                            class="px-2 py-1 rounded bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 
                                   hover:bg-gray-300 dark:hover:bg-gray-600 transition flex items-center gap-2"
                            title="Переключить тему">
                        <span x-text="isDark ? 'Светлая' : 'Тёмная'"></span>
                    </button>

                    <!-- Выпадающее меню профиля -->
                    <div class="relative" x-data="{ open: false }">

                        <button @click="open = !open" 
                                class="flex items-center space-x-2 text-gray-700 dark:text-gray-200 
                                       hover:text-gray-900 dark:hover:text-white focus:outline-none">
                            <span class="font-medium">{{ auth()->user()->name }}</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="open"
                             @click.away="open = false"
                             x-transition
                             class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg py-1 z-10 border border-gray-200 dark:border-gray-700">

                            <a href="{{ route('profile') }}"
                               class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 
                                      hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                👤 Профиль
                            </a>

                            @if(auth()->user()->hasRole('admin'))
                            <a href="{{ route('admin.dashboard') }}"
                               class="block px-4 py-2 text-sm text-blue-600 dark:text-blue-400 
                                      hover:bg-blue-50 dark:hover:bg-gray-700 font-semibold transition-colors">
                                ⚙️ Админка
                            </a>
                            @endif

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="block w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 
                                               hover:bg-red-50 dark:hover:bg-gray-700 transition-colors">
                                    🚪 Выйти
                                </button>
                            </form>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </nav>

    <!-- Основной контент -->
    <main class="min-h-screen">
        {{ $slot }}
    </main>

    <!-- Футер -->
    <footer class="bg-white dark:bg-gray-800 py-12 border-t border-gray-200 dark:border-gray-700 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Лого и описание -->
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-amber-600 to-amber-800 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="text-amber-100 text-lg font-bold">🎱</span>
                        </div>
                        <span class="text-2xl font-bold text-gray-800 dark:text-gray-100">Бильярд Клуб</span>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 max-w-md">
                        Премиум бильярдный клуб с безупречным сервисом и профессиональным оборудованием. 
                        Играйте с комфортом и стилем.
                    </p>
                </div>
                
                <!-- Навигация -->
                <div>
                    <h4 class="text-gray-800 dark:text-amber-200 font-semibold mb-4">Навигация</h4>
                    <ul class="space-y-2">
                        <li><a href="/" class="text-gray-600 dark:text-gray-400 hover:text-amber-600 dark:hover:text-amber-300 transition-colors">Главная</a></li>
                        @auth
                            <li><a href="{{ route('dashboard') }}" class="text-gray-600 dark:text-gray-400 hover:text-amber-600 dark:hover:text-amber-300 transition-colors">Личный кабинет</a></li>
                        @else
                            <li><a href="{{ route('login') }}" class="text-gray-600 dark:text-gray-400 hover:text-amber-600 dark:hover:text-amber-300 transition-colors">Войти</a></li>
                            <li><a href="{{ route('register') }}" class="text-gray-600 dark:text-gray-400 hover:text-amber-600 dark:hover:text-amber-300 transition-colors">Регистрация</a></li>
                        @endauth
                    </ul>
                </div>
                
                <!-- Контакты -->
                <div>
                    <h4 class="text-gray-800 dark:text-amber-200 font-semibold mb-4">Контакты</h4>
                    <ul class="space-y-2 text-gray-600 dark:text-gray-400">
                        <li class="flex items-center gap-2">
                            <span class="text-amber-600 dark:text-amber-400">📞</span>
                            +7 (XXX) XXX-XX-XX
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-amber-600 dark:text-amber-400">✉️</span>
                            info@billiard-club.ru
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-amber-600 dark:text-amber-400">📍</span>
                            г. Москва, ул. Примерная, 123
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Копирайт -->
            <div class="border-t border-gray-200 dark:border-gray-700 mt-8 pt-8 text-center text-gray-500 dark:text-gray-400">
                <p>&copy; 2024 Бильярд Клуб. Все права защищены.</p>
            </div>
        </div>
    </footer>

    <!-- Скрипт темы -->
    <script>
        function themeData() {
            return {
                isDark: false,
                initTheme() {
                    // Сначала проверяем localStorage
                    const saved = localStorage.getItem("theme");
                    
                    if (saved) {
                        this.isDark = saved === "dark";
                    } else {
                        // Если нет сохранённой темы — берём тему системы
                        this.isDark = window.matchMedia("(prefers-color-scheme: dark)").matches;
                    }
                    
                    // Применяем тему сразу
                    document.documentElement.classList.toggle("dark", this.isDark);
                    
                    // Убираем cloaking после инициализации
                    setTimeout(() => {
                        document.body.classList.remove('theme-loading');
                        document.body.classList.add('theme-loaded');
                    }, 50);
                },
                toggleTheme() {
                    this.isDark = !this.isDark;
                    document.documentElement.classList.toggle("dark", this.isDark);
                    localStorage.setItem("theme", this.isDark ? "dark" : "light");
                }
            }
        }

        // Fallback: если Alpine не загрузился, всё равно показываем контент
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                document.body.classList.remove('theme-loading');
                document.body.classList.add('theme-loaded');
                document.body.removeAttribute('x-cloak');
            }, 1000);
        });
    </script>
</body>
</html>