<!-- Sidebar -->
<div class="w-64 bg-gray-900 text-white flex flex-col h-screen">
    <!-- Header -->
    <div class="p-6 border-b border-gray-700">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10  rounded-lg flex items-center justify-center">
                <span class="text-2xl">🎱</span>
            </div>
            <div>
                <h1 class="text-lg font-bold">Бильярд</h1>
                <p class="text-xs text-gray-400">Админ-панель</p>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto py-4">
        <!-- Dashboard Section -->
        <div class="mb-6">
            <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                Главное
            </div>
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center py-3 px-4 hover:bg-gray-700/50 transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700 border-r-4 border-blue-500' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span>Дашборд</span>
            </a>
        </div>

<<<<<<< HEAD
        <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase">Управление</div>
        <a href="{{ route('admin.users.index') }}" class="block py-3 px-4 hover:bg-gray-700 {{ request()->routeIs('admin.users.*') ? 'bg-gray-700 border-r-4 border-blue-500' : '' }}">
            Пользователи
        </a>
        <a href="{{ route('admin.bookings.index') }}" class="block py-3 px-4 hover:bg-gray-700 {{ request()->routeIs('admin.bookings.*') ? 'bg-gray-700 border-r-4 border-blue-500' : '' }}">
            Бронирования
        </a>
        <a href="{{ route('admin.orders.index') }}" class="block py-3 px-4 hover:bg-gray-700 {{ request()->routeIs('admin.orders.*') ? 'bg-gray-700 border-r-4 border-blue-500' : '' }}">
            Заказы
        </a>
        <a href="{{ route('admin.product-types.index') }}" class="block py-3 px-4 hover:bg-gray-700 {{ request()->routeIs('admin.product-types.*') ? 'bg-gray-700 border-r-4 border-blue-500' : '' }}">
            Типы продуктов
        </a>
        <a href="{{ route('admin.product-models.index') }}" class="block py-3 px-4 hover:bg-gray-700 {{ request()->routeIs('admin.product-models.*') ? 'bg-gray-700 border-r-4 border-blue-500' : '' }}">
            Модели продуктов
        </a>
        <a href="{{ route('admin.places.list') }}" class="block py-3 px-4 hover:bg-gray-700 {{ request()->routeIs('admin.places.*') ? 'bg-gray-700 border-r-4 border-blue-500' : '' }}">
            Места
        </a>
        <a href="{{ route('admin.zones.index') }}" class="block py-3 px-4 hover:bg-gray-700 {{ request()->routeIs('admin.zones.*') ? 'bg-gray-700 border-r-4 border-blue-500' : '' }}">
            Зоны
        </a>
        <a href="{{ route('admin.price-rules.index') }}" class="block py-3 px-4 hover:bg-gray-700 {{ request()->routeIs('admin.price-rules.*') ? 'bg-gray-700 border-r-4 border-blue-500' : '' }}">
            Правила цен
        </a>
        <a href="{{ route('admin.resources.index') }}" class="block py-3 px-4 hover:bg-gray-700 {{ request()->routeIs('admin.resources.*') ? 'bg-gray-700 border-r-4 border-blue-500' : '' }}">
            Ресурсы
        </a>
=======
        <!-- Bookings Section -->
        <div class="mb-6">
            <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                Бронирования
            </div>
            <a href="{{ route('admin.bookings.index') }}"
               class="flex items-center py-3 px-4 hover:bg-gray-700/50 transition-colors {{ request()->routeIs('admin.bookings.*') ? 'bg-gray-700 border-r-4 border-blue-500' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>Все бронирования</span>
            </a>
        </div>
>>>>>>> 9de906e554df162a98efec79179ff642de1b5e20

        <!-- Places & Resources Section -->
        <div class="mb-6">
            <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                Помещения
            </div>
            <a href="{{ route('admin.places.index') }}"
               class="flex items-center py-3 px-4 hover:bg-gray-700/50 transition-colors {{ request()->routeIs('admin.places.*') ? 'bg-gray-700 border-r-4 border-blue-500' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span>Места</span>
            </a>
            <a href="{{ route('admin.zones.index') }}"
               class="flex items-center py-3 px-4 hover:bg-gray-700/50 transition-colors {{ request()->routeIs('admin.zones.index') ? 'bg-gray-700 border-r-4 border-blue-500' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                </svg>
                <span>Зоны</span>
            </a>
            <a href="{{ route('admin.resources.index') }}"
               class="flex items-center py-3 px-4 hover:bg-gray-700/50 transition-colors {{ request()->routeIs('admin.resources.index') ? 'bg-gray-700 border-r-4 border-blue-500' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                </svg>
                <span>Ресурсы</span>
            </a>
        </div>

        <!-- Visual Editors Section -->
        <div class="mb-6">
            <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                Редакторы
            </div>
            <a href="{{ route('admin.zones.editor') }}"
               class="flex items-center py-3 px-4 hover:bg-gray-700/50 transition-colors {{ request()->routeIs('admin.zones.editor') ? 'bg-gray-700 border-r-4 border-blue-500' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <span>Редактор зон</span>
            </a>
            <a href="{{ route('admin.tables.editor') }}"
               class="flex items-center py-3 px-4 hover:bg-gray-700/50 transition-colors {{ request()->routeIs('admin.tables.editor') ? 'bg-gray-700 border-r-4 border-blue-500' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                </svg>
                <span>Редактор столов</span>
            </a>
        </div>

        <!-- Products Section -->
        <div class="mb-6">
            <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                Продукты
            </div>
            <a href="{{ route('admin.product-types.index') }}"
               class="flex items-center py-3 px-4 hover:bg-gray-700/50 transition-colors {{ request()->routeIs('admin.product-types.*') ? 'bg-gray-700 border-r-4 border-blue-500' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                <span>Типы продуктов</span>
            </a>
            <a href="{{ route('admin.product-models.index') }}"
               class="flex items-center py-3 px-4 hover:bg-gray-700/50 transition-colors {{ request()->routeIs('admin.product-models.*') ? 'bg-gray-700 border-r-4 border-blue-500' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <span>Модели продуктов</span>
            </a>
        </div>

        <!-- Pricing Section -->
        <div class="mb-6">
            <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                Цены
            </div>
            <a href="{{ route('admin.price-rules.index') }}"
               class="flex items-center py-3 px-4 hover:bg-gray-700/50 transition-colors {{ request()->routeIs('admin.price-rules.*') ? 'bg-gray-700 border-r-4 border-blue-500' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Правила цен</span>
            </a>
        </div>

        <!-- Users Section -->
        <div class="mb-6">
            <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                Пользователи
            </div>
            <a href="{{ route('admin.users.index') }}"
               class="flex items-center py-3 px-4 hover:bg-gray-700/50 transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-gray-700 border-r-4 border-blue-500' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <span>Пользователи</span>
            </a>
        </div>
    </nav>

    <!-- Footer -->
    <div class="p-4 border-t border-gray-700">
        <a href="/" target="_blank"
           class="flex items-center py-2 px-3 hover:bg-gray-700/50 rounded transition-colors text-sm">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
            </svg>
            <span>Перейти на сайт</span>
        </a>
        
        <div class="mt-3 pt-3 border-t border-gray-700">
            <div class="flex items-center text-xs text-gray-400">
                <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                <span>Система активна</span>
            </div>
        </div>
    </div>
</div>