<div class="max-w-7xl mx-auto p-6">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-3">Добро пожаловать, {{ auth()->user()->name }}!</h1>
        <p class="text-xl text-gray-600 dark:text-gray-400">Здесь вы можете управлять своими бронированиями</p>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="mb-8 p-6 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-950/40 dark:to-emerald-950/30 rounded-xl border-2 border-green-200 dark:border-green-800">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/40 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-green-800 dark:text-green-200">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-8 p-6 bg-gradient-to-r from-red-50 to-pink-50 dark:from-red-950/40 dark:to-pink-950/30 rounded-xl border-2 border-red-200 dark:border-red-800">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-red-100 dark:bg-red-900/40 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-red-800 dark:text-red-200">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Статистика -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg dark:shadow-gray-900/30 p-6 border border-gray-200 dark:border-gray-700 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">Всего бронирований</h3>
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/40 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">За все время</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg dark:shadow-gray-900/30 p-6 border border-gray-200 dark:border-gray-700 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">Активных</h3>
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/40 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $stats['active'] }}</div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Текущие брони</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg dark:shadow-gray-900/30 p-6 border border-gray-200 dark:border-gray-700 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">Завершено</h3>
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/40 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['completed'] }}</div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Успешные игры</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg dark:shadow-gray-900/30 p-6 border border-gray-200 dark:border-gray-700 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">Потрачено</h3>
                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/40 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-bold text-purple-600 dark:text-purple-400">
                {{ number_format($stats['total_spent'], 0, '', ' ') }} ₽
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Общие расходы</p>
        </div>
    </div>

    <!-- Быстрые действия -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white hover:shadow-2xl transition-all duration-300 transform hover:scale-105 group">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold mb-3">Новое бронирование</h3>
            <p class="mb-4 opacity-90 text-blue-100">Забронировать стол на нужное время</p>
            <a href="{{ route('booking.create') }}" class="inline-flex items-center gap-2 text-white hover:text-blue-100 font-medium transition-colors">
                <span>Создать бронь</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white hover:shadow-2xl transition-all duration-300 transform hover:scale-105 group">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold mb-3">Мой профиль</h3>
            <p class="mb-4 opacity-90 text-green-100">Управление личными данными</p>
            <a href="{{ route('profile') }}" class="inline-flex items-center gap-2 text-white hover:text-green-100 font-medium transition-colors">
                <span>Перейти</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>

        @if(auth()->user()->hasRole('admin'))
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white hover:shadow-2xl transition-all duration-300 transform hover:scale-105 group">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold mb-3">Админ-панель</h3>
            <p class="mb-4 opacity-90 text-purple-100">Управление системой</p>
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 text-white hover:text-purple-100 font-medium transition-colors">
                <span>Перейти</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
        @else
        <div class="bg-gradient-to-br from-gray-500 to-gray-600 rounded-xl shadow-lg p-6 text-white hover:shadow-2xl transition-all duration-300 transform hover:scale-105 group">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold mb-3">Поддержка</h3>
            <p class="mb-4 opacity-90 text-gray-100">Нужна помощь с бронированием?</p>
            <span class="text-sm opacity-75">Свяжитесь с нами</span>
        </div>
        @endif
    </div>

    <!-- Мои бронирования -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg dark:shadow-gray-900/30 p-6 border border-gray-200 dark:border-gray-700">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Мои бронирования</h2>
                <p class="text-gray-600 dark:text-gray-400">История и текущие бронирования</p>
            </div>
            <button wire:click="$refresh" 
                    class="flex items-center gap-2 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 px-4 py-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors border border-blue-200 dark:border-blue-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span class="font-medium">Обновить</span>
            </button>
        </div>

        @if($recentBookings->count() > 0)
            <div class="space-y-6">
                @foreach($recentBookings as $booking)
                <div class="border-2 border-gray-200 dark:border-gray-700 rounded-xl p-6 hover:border-blue-300 dark:hover:border-blue-500 hover:shadow-lg dark:hover:shadow-blue-900/20 transition-all duration-300 group cursor-pointer">
                    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start gap-6">
                        <!-- Левая часть: Информация о бронировании -->
                        <div class="flex-1">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/40 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-xl text-gray-900 dark:text-white">
                                            {{ $booking->place->name }}
                                        </h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $booking->place->address }}</p>
                                    </div>
                                </div>
                                <div class="lg:hidden">
                                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full
                                        @if($booking->status === 'confirmed') bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-200
                                        @elseif($booking->status === 'pending') bg-yellow-100 dark:bg-yellow-900/40 text-yellow-800 dark:text-yellow-200
                                        @elseif($booking->status === 'canceled') bg-gray-100 dark:bg-gray-700 text-red-600 dark:text-red-400
                                        @elseif($booking->status === 'finished') bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-200
                                        @else bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                        @endif">
                                        {{ $booking->status }}
                                    </span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Стол -->
                                <div class="flex items-center text-sm">
                                    <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <span class="text-gray-600 dark:text-gray-400">Стол:</span>
                                        <span class="font-medium text-gray-900 dark:text-white ml-2">
                                            {{ $booking->resource->code ?? 'N/A' }} - {{ $booking->resource->model->name ?? 'N/A' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Дата создания -->
                                <div class="flex items-center text-sm">
                                    <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <span class="text-gray-600 dark:text-gray-400">Создано:</span>
                                        <span class="font-medium text-gray-900 dark:text-white ml-2">
                                            {{ $booking->created_at->format('d.m.Y H:i') }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Временные слоты -->
                                <div class="flex items-start text-sm md:col-span-2">
                                    <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center mr-3 mt-1">
                                        <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <span class="text-gray-600 dark:text-gray-400">Время:</span>
                                        <div class="flex flex-wrap gap-2 mt-2">
                                            @foreach($booking->slots->take(5) as $slot)
                                                <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-200 rounded-lg text-sm font-medium">
                                                    {{ \Carbon\Carbon::parse($slot->slot_datetime)->format('d.m H:i') }}
                                                </span>
                                            @endforeach
                                            @if($booking->slots->count() > 5)
                                                <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-lg text-sm">
                                                    +{{ $booking->slots->count() - 5 }} ещё
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Оборудование -->
                                @if($booking->equipment->count() > 0)
                                <div class="flex items-start text-sm md:col-span-2">
                                    <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center mr-3 mt-1">
                                        <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <span class="text-gray-600 dark:text-gray-400">Оборудование:</span>
                                        <div class="flex flex-wrap gap-2 mt-2">
                                            @foreach($booking->equipment as $item)
                                                <span class="px-3 py-1 bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-200 rounded-lg text-sm font-medium">
                                                    {{ $item->productModel->name ?? 'N/A' }} ×{{ $item->qty ?? 1 }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Правая часть: Статус и действия -->
                        <div class="flex flex-col items-start lg:items-end space-y-4 lg:min-w-[200px]">
                            <!-- Статус (скрыт на мобильных) -->
                            <div class="hidden lg:block w-full text-right">
                                <span class="inline-block px-4 py-2 text-sm font-semibold rounded-full
                                    @if($booking->status === 'confirmed') bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-200
                                    @elseif($booking->status === 'pending') bg-yellow-100 dark:bg-yellow-900/40 text-yellow-800 dark:text-yellow-200
                                    @elseif($booking->status === 'canceled') bg-gray-100 dark:bg-gray-700 text-red-600 dark:text-red-400
                                    @elseif($booking->status === 'finished') bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-200
                                    @else bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                    @endif">
                                    {{ $booking->status }}
                                </span>
                            </div>

                            <!-- Статус оплаты -->
                            <div class="w-full text-right">
                                <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full
                                    @if($booking->payment_status === 'paid') bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-200
                                    @elseif($booking->payment_status === 'pending') bg-yellow-100 dark:bg-yellow-900/40 text-yellow-800 dark:text-yellow-200
                                    @else bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                    @endif">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                    </svg>
                                    {{ $booking->payment_status }}
                                </span>
                            </div>

                            <!-- Сумма -->
                            <div class="w-full text-right">
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ number_format($booking->total_amount / 100, 0, '', ' ') }} ₽
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $booking->slots->count() }} час(ов)</p>
                            </div>

                            <!-- Действия -->
                            <div class="w-full text-right">
                                @if($booking->payment_status === 'pending' && in_array($booking->status, ['pending', 'confirmed']))
                                    <button wire:click="cancelBooking({{ $booking->id }})"
                                            wire:confirm="Вы уверены, что хотите отменить это бронирование?"
                                            class="inline-flex items-center gap-2 px-4 py-2 bg-red-500 hover:bg-red-600 dark:bg-red-600 dark:hover:bg-red-700 text-white text-sm rounded-lg transition-colors font-medium">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Отменить
                                    </button>
                                @endif
                            </div>

                            <!-- Истекает через -->
                            @if($booking->expires_at && $booking->payment_status === 'pending')
                                <div class="w-full text-right text-sm text-orange-600 dark:text-orange-400 font-medium flex items-center gap-2 justify-end">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Истекает: {{ $booking->expires_at->diffForHumans() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-16 text-gray-500 dark:text-gray-400">
                <div class="w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <p class="text-2xl font-medium mb-3">У вас пока нет бронирований</p>
                <p class="text-gray-400 dark:text-gray-500 mb-6">Создайте первое бронирование, чтобы начать играть!</p>
                <a href="{{ route('booking.create') }}" 
                   class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold transition-colors">
                    <span>Создать бронирование</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        @endif
    </div>
</div>