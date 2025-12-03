<!DOCTYPE html>
<html lang="ru" x-data="themeData()" x-init="initTheme()" :class="{ 'dark': isDark }" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'Бильярд Клуб') }}</title>

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
<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 transition-colors duration-300 theme-loading"
      x-bind:class="isDark ? 'theme-loaded' : 'theme-loaded'"
      x-cloak>

    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <!-- Логотип и заголовок -->
        <div class="mb-8 text-center">
            <a href="{{ route('home') }}" class="flex flex-col items-center space-y-4 group" wire:navigate>
                <div class="w-16 h-16 bg-gradient-to-br from-amber-600 to-amber-800 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform shadow-lg">
                    <div class="w-10 h-10 bg-black rounded-full flex items-center justify-center relative">
                        <div class="absolute inset-0 rounded-full border border-amber-200/30"></div>
                        <span class="text-white text-sm font-bold z-10">8</span>
                    </div>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 dark:text-amber-100">
                        Бильярд Клуб
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">
                        Премиальный клуб для истинных ценителей
                    </p>
                </div>
            </a>
        </div>

        <!-- Контейнер формы -->
        <div class="w-full sm:max-w-md mt-6 px-6">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-gray-200 dark:border-gray-700">
                <div class="h-2 bg-gradient-to-r from-amber-600 to-amber-800"></div>
                
                <div class="px-6 py-8">
                    {{ $slot }}
                </div>
                
                <!-- Кнопка переключения темы  -->
                <div class="px-6 pb-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button @click="toggleTheme()" 
                            class="w-full flex items-center justify-center gap-2 px-4 py-2 rounded-lg 
                                   bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 
                                   hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                            title="Переключить тему">
                        <svg x-show="!isDark" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
                        </svg>
                        <svg x-show="isDark" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/>
                        </svg>
                        <span x-text="isDark ? 'Светлая тема' : 'Тёмная тема'"></span>
                    </button>
                </div>
            </div>
            
            <!-- Дополнительная информация -->
            <div class="mt-8 text-center text-sm text-gray-500 dark:text-gray-400">
                <p>По вопросам обращайтесь: 
                    <a href="tel:+78001234567" class="text-amber-600 dark:text-amber-400 hover:underline">
                        +7 (800) 123-45-67
                    </a>
                </p>
                <p class="mt-1">Работаем ежедневно с 10:00 до 02:00</p>
            </div>
        </div>
    </div>

    <!-- Скрипт темы -->
    <script>
        function themeData() {
            return {
                isDark: false,
                initTheme() {

                    const saved = localStorage.getItem("theme");
                    
                    if (saved) {
                        this.isDark = saved === "dark";
                    } else {

                        this.isDark = window.matchMedia("(prefers-color-scheme: dark)").matches;
                    }
                    

                    document.documentElement.classList.toggle("dark", this.isDark);
                    

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