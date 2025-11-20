<div>
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Добро пожаловать, {{ auth()->user()->name }}! 👋</h1>
        <p class="text-gray-600 mt-2">Здесь вы можете управлять своими бронированиями</p>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <!-- Статистика -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-600">Всего бронирований</h3>
                <span class="text-2xl">📅</span>
            </div>
            <div class="text-3xl font-bold text-gray-800">{{ $stats['total'] }}</div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-600">Активных</h3>
                <span class="text-2xl">✅</span>
            </div>
            <div class="text-3xl font-bold text-green-600">{{ $stats['active'] }}</div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-600">Завершено</h3>
                <span class="text-2xl">🏁</span>
            </div>
            <div class="text-3xl font-bold text-blue-600">{{ $stats['completed'] }}</div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-600">Потрачено</h3>
                <span class="text-2xl">💰</span>
            </div>
            <div class="text-3xl font-bold text-purple-600">
                {{ number_format($stats['total_spent'], 0, '', ' ') }} ₽
            </div>
        </div>
    </div>

    <!-- Быстрые действия -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-md p-6 text-white hover:shadow-lg transition">
            <div class="text-4xl mb-4">➕</div>
            <h3 class="text-xl font-bold mb-2">Новое бронирование</h3>
            <p class="mb-4 opacity-90">Забронировать стол на нужное время</p>
            <span class="text-sm opacity-75">(Скоро доступно)</span>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-md p-6 text-white hover:shadow-lg transition">
            <div class="text-4xl mb-4">👤</div>
            <h3 class="text-xl font-bold mb-2">Мой профиль</h3>
            <p class="mb-4 opacity-90">Управление личными данными</p>
            <a href="{{ route('profile') }}" class="text-white hover:underline font-medium">
                Перейти →
            </a>
        </div>

        @if(auth()->user()->hasRole('admin'))
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-md p-6 text-white hover:shadow-lg transition">
            <div class="text-4xl mb-4">⚙️</div>
            <h3 class="text-xl font-bold mb-2">Админ-панель</h3>
            <p class="mb-4 opacity-90">Управление системой</p>
            <a href="{{ route('admin.dashboard') }}" class="text-white hover:underline font-medium">
                Перейти →
            </a>
        </div>
        @endif
    </div>

    <!-- Мои бронирования -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Мои бронирования</h2>
            <button wire:click="$refresh" 
                    class="text-blue-600 hover:text-blue-800 text-sm flex items-center space-x-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <span>Обновить</span>
            </button>
        </div>

        @if($recentBookings->count() > 0)
            <div class="space-y-4">
                @foreach($recentBookings as $booking)
                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start">
                        <!-- Левая часть: Информация о бронировании -->
                        <div class="flex-1">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <h3 class="font-semibold text-lg text-gray-800">
                                        🏢 {{ $booking->place->name }}
                                    </h3>
                                    <p class="text-sm text-gray-500">{{ $booking->place->address }}</p>
                                </div>
                                <div class="lg:hidden ml-4">
                                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded
                                        @if($booking->status === 'confirmed') bg-green-100 text-green-800
                                        @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($booking->status === 'canceled') bg-red-100 text-red-800
                                        @elseif($booking->status === 'finished') bg-blue-100 text-blue-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ $booking->status }}
                                    </span>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <!-- Стол -->
                                <div class="flex items-center text-sm">
                                    <span class="text-gray-600 mr-2">🎱 Стол:</span>
                                    <span class="font-medium">
                                        {{ $booking->resource->code ?? 'N/A' }} - {{ $booking->resource->model->name }}
                                    </span>
                                </div>

                                <!-- Дата создания -->
                                <div class="flex items-center text-sm">
                                    <span class="text-gray-600 mr-2">📅 Создано:</span>
                                    <span class="font-medium">{{ $booking->created_at->format('d.m.Y H:i') }}</span>
                                </div>

                                <!-- Временные слоты -->
                                <div class="flex items-start text-sm">
                                    <span class="text-gray-600 mr-2 mt-1">🕐 Время:</span>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($booking->slots->take(5) as $slot)
                                            <span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded text-xs">
                                                {{ \Carbon\Carbon::parse($slot->slot_datetime)->format('d.m H:i') }}
                                            </span>
                                        @endforeach
                                        @if($booking->slots->count() > 5)
                                            <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">
                                                +{{ $booking->slots->count() - 5 }} ещё
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Оборудование -->
                                @if($booking->equipment->count() > 0)
                                <div class="flex items-start text-sm">
                                    <span class="text-gray-600 mr-2 mt-1">📦 Оборудование:</span>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($booking->equipment as $item)
                                            <span class="px-2 py-0.5 bg-green-100 text-green-800 rounded text-xs">
                                                {{ $item->productModel->name }} ×{{ $item->qty }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Правая часть: Статус и действия -->
                        <div class="mt-4 lg:mt-0 lg:ml-6 flex flex-col items-end space-y-3">
                            <!-- Статус (скрыт на мобильных) -->
                            <div class="hidden lg:block">
                                <span class="inline-block px-3 py-1 text-sm font-semibold rounded
                                    @if($booking->status === 'confirmed') bg-green-100 text-green-800
                                    @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($booking->status === 'canceled') bg-red-100 text-red-800
                                    @elseif($booking->status === 'finished') bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $booking->status }}
                                </span>
                            </div>

                            <!-- Статус оплаты -->
                            <div>
                                <span class="inline-block px-3 py-1 text-xs font-semibold rounded
                                    @if($booking->payment_status === 'paid') bg-green-100 text-green-800
                                    @elseif($booking->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    💳 {{ $booking->payment_status }}
                                </span>
                            </div>

                            <!-- Сумма -->
                            <div class="text-right">
                                <p class="text-2xl font-bold text-gray-800">
                                    {{ number_format($booking->total_amount / 100, 0, '', ' ') }} ₽
                                </p>
                                <p class="text-xs text-gray-500">{{ $booking->slots->count() }} час(ов)</p>
                            </div>

                            <!-- Действия -->
                            <div class="flex space-x-2">
                                @if($booking->payment_status === 'pending' && in_array($booking->status, ['pending', 'confirmed']))
                                    <button wire:click="cancelBooking({{ $booking->id }})"
                                            wire:confirm="Вы уверены, что хотите отменить это бронирование?"
                                            class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-sm rounded transition">
                                        ❌ Отменить
                                    </button>
                                @endif

                            
                            </div>

                            <!-- Истекает через -->
                            @if($booking->expires_at && $booking->payment_status === 'pending')
                                <div class="text-xs text-orange-600">
                                    ⏰ Истекает: {{ $booking->expires_at->diffForHumans() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 text-gray-500">
                <div class="text-6xl mb-4">📭</div>
                <p class="text-xl font-medium mb-2">У вас пока нет бронирований</p>
                <p class="text-gray-400">Создайте первое бронирование, чтобы начать играть!</p>
            </div>
        @endif
    </div>
</div>