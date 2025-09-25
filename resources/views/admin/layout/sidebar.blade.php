<!-- Sidebar -->
<div class="w-64 bg-gray-800 text-white">
    <div class="p-4">
        <h1 class="text-xl font-bold">🎱 Бильярд Админ</h1>
        <p class="text-sm text-gray-400">Панель управления</p>
    </div>
    
    <nav class="mt-6">
        <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase">Главное</div>
        <a href="{{ route('admin.dashboard') }}" 
           class="block py-3 px-4 hover:bg-gray-700 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700 border-r-4 border-blue-500' : '' }}">
            📊 Дашборд
        </a>
        
        <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase">Управление</div>
        <a href="{{ route('admin.users.index') }}" 
           class="block py-3 px-4 hover:bg-gray-700 {{ request()->routeIs('admin.users.*') ? 'bg-gray-700 border-r-4 border-blue-500' : '' }}">
            👥 Пользователи
        </a>
        <a href="{{ route('admin.bookings.index') }}" 
           class="block py-3 px-4 hover:bg-gray-700 {{ request()->routeIs('admin.bookings.*') ? 'bg-gray-700 border-r-4 border-blue-500' : '' }}">
            📅 Бронирования
        </a>
        <a href="{{ route('admin.orders.index') }}" 
           class="block py-3 px-4 hover:bg-gray-700 {{ request()->routeIs('admin.orders.*') ? 'bg-gray-700 border-r-4 border-blue-500' : '' }}">
            🛒 Заказы
        </a>
        <a href="{{ route('admin.payments.index') }}" 
           class="block py-3 px-4 hover:bg-gray-700 {{ request()->routeIs('admin.payments.*') ? 'bg-gray-700 border-r-4 border-blue-500' : '' }}">
            💳 Платежи
        </a>
        
        <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase">Система</div>
        <a href="/" target="_blank" class="block py-3 px-4 hover:bg-gray-700">
            🌐 Сайт
        </a>
    </nav>
</div>