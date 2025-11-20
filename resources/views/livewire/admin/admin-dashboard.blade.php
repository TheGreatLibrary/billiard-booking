<div>
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">📊 Панель управления</h1>
            <p class="text-gray-600">Обзор системы бронирования</p>
        </div>
        <button wire:click="loadStatistics" 
                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition"
                wire:loading.attr="disabled">
            <span wire:loading.remove>🔄 Обновить</span>
            <span wire:loading>⏳ Загрузка...</span>
        </button>
    </div>

    <!-- Основные метрики -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Бронирования -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-gray-700">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium">Бронирования</h3>
                <span class="text-2xl">📅</span>
            </div>
            <div class="text-3xl font-bold mb-1">{{ $total['bookings'] }}</div>
            <div class="text-sm text-gray-700e opacity-90">
                <span class="font-semibold">{{ $total['bookings_paid'] }}</span> оплачено · 
                <span class="font-semibold">{{ $total['bookings_pending'] }}</span> ожидает
            </div>
        </div>

        <!-- Выручка -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-gray-700">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium">Выручка</h3>
                <span class="text-2xl">💰</span>
            </div>
            <div class="text-3xl font-bold mb-1">{{ number_format($total['amount'], 0, '', ' ') }} ₽</div>
            <div class="text-sm text-gray-700 opacity-90">Только оплаченные</div>
        </div>

        <!-- Пользователи -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-gray-700">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium">Пользователи</h3>
                <span class="text-2xl">👥</span>
            </div>
            <div class="text-3xl font-bold mb-1">{{ $total['users'] }}</div>
            <div class="text-sm text-gray-700 opacity-90">Зарегистрировано</div>
        </div>

        <!-- Столы -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-6 text-gray-700">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium">Столы</h3>
                <span class="text-2xl">🎱</span>
            </div>
            <div class="text-3xl font-bold mb-1">{{ $total['resources'] }}</div>
            <div class="text-sm text-gray-700 opacity-90">Всего ресурсов</div>
        </div>
    </div>

    <!-- Статистика по статусам -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Статусы бронирований -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">📈 Статусы бронирований</h2>
            <div class="grid grid-cols-2 gap-4">
                <div class="text-center p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                    <div class="text-3xl font-bold text-yellow-600">{{ $statusStats['pending'] ?? 0 }}</div>
                    <div class="text-sm text-gray-700 mt-1 font-medium">Ожидание</div>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg border border-green-200">
                    <div class="text-3xl font-bold text-green-600">{{ $statusStats['confirmed'] ?? 0 }}</div>
                    <div class="text-sm text-gray-700 mt-1 font-medium">Подтверждено</div>
                </div>
                <div class="text-center p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="text-3xl font-bold text-blue-600">{{ $statusStats['finished'] ?? 0 }}</div>
                    <div class="text-sm text-gray-700 mt-1 font-medium">Завершено</div>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="text-3xl font-bold text-gray-600">{{ $statusStats['canceled'] ?? 0 }}</div>
                    <div class="text-sm text-gray-700 mt-1 font-medium">Отменено</div>
                </div>
            </div>
        </div>

        <!-- Статусы оплаты -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">💳 Статусы оплаты</h2>
            <div class="grid grid-cols-2 gap-4">
                <div class="text-center p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                    <div class="text-3xl font-bold text-yellow-600">{{ $paymentStatusStats['pending'] ?? 0 }}</div>
                    <div class="text-sm text-gray-700 mt-1 font-medium">Ожидает оплаты</div>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg border border-green-200">
                    <div class="text-3xl font-bold text-green-600">{{ $paymentStatusStats['paid'] ?? 0 }}</div>
                    <div class="text-sm text-gray-700 mt-1 font-medium">Оплачено</div>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="text-3xl font-bold text-gray-600">{{ $paymentStatusStats['canceled'] ?? 0 }}</div>
                    <div class="text-sm text-gray-700 mt-1 font-medium">Отменено</div>
                </div>
                <div class="text-center p-4 bg-purple-50 rounded-lg border border-purple-200">
                    <div class="text-3xl font-bold text-purple-600">{{ $paymentStatusStats['refunded'] ?? 0 }}</div>
                    <div class="text-sm text-gray-700 mt-1 font-medium">Возврат</div>
                </div>
            </div>
        </div>
    </div>

    <!-- График по месяцам -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4 text-gray-800">📊 Динамика оплат по месяцам</h2>
        
        @if($monthly->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Месяц</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Количество</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Сумма</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">График</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @php $maxAmount = $monthly->max('amount') ?? 1; @endphp
                        @foreach($monthly as $month => $data)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $month }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $data->count }}</td>
                            <td class="px-4 py-3 font-semibold text-green-600">{{ number_format($data->amount, 2, ',', ' ') }} ₽</td>
                            <td class="px-4 py-3">
                                <div class="w-full bg-gray-200 rounded-full h-4">
                                    <div class="bg-green-500 h-4 rounded-full transition-all" 
                                         style="width: {{ ($data->amount / $maxAmount) * 100 }}%"></div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 font-bold">
                        <tr>
                            <td class="px-4 py-3 text-gray-800">ИТОГО:</td>
                            <td class="px-4 py-3 text-gray-800">{{ $monthly->sum('count') }}</td>
                            <td class="px-4 py-3 text-green-600">{{ number_format($monthly->sum('amount'), 2, ',', ' ') }} ₽</td>
                            <td class="px-4 py-3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <div class="text-4xl mb-2">📊</div>
                <p>Нет данных по оплатам</p>
            </div>
        @endif
    </div>

    <!-- Справочники -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4 text-gray-800">🗂️ Справочники</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('admin.places.list') }}" 
               class="p-4 bg-blue-50 hover:bg-blue-100 rounded-lg border border-blue-200 transition">
                <div class="text-2xl font-bold text-blue-600">{{ $total['places'] }}</div>
                <div class="text-sm text-gray-700 mt-1 font-medium">🏢 Места</div>
            </a>
            <a href="{{ route('admin.zones.index') }}" 
               class="p-4 bg-purple-50 hover:bg-purple-100 rounded-lg border border-purple-200 transition">
                <div class="text-2xl font-bold text-purple-600">{{ $total['zones'] }}</div>
                <div class="text-sm text-gray-700 mt-1 font-medium">📍 Зоны</div>
            </a>
            <a href="{{ route('admin.resources.index') }}" 
               class="p-4 bg-green-50 hover:bg-green-100 rounded-lg border border-green-200 transition">
                <div class="text-2xl font-bold text-green-600">{{ $total['resources'] }}</div>
                <div class="text-sm text-gray-700 mt-1 font-medium">🎱 Столы</div>
            </a>
            <a href="{{ route('admin.product-models.index') }}" 
               class="p-4 bg-orange-50 hover:bg-orange-100 rounded-lg border border-orange-200 transition">
                <div class="text-2xl font-bold text-orange-600">{{ $total['productModels'] }}</div>
                <div class="text-sm text-gray-700 mt-1 font-medium">📦 Товары</div>
            </a>
            <a href="{{ route('admin.product-types.index') }}" 
               class="p-4 bg-pink-50 hover:bg-pink-100 rounded-lg border border-pink-200 transition">
                <div class="text-2xl font-bold text-pink-600">{{ $total['productTypes'] }}</div>
                <div class="text-sm text-gray-700 mt-1 font-medium">🏷️ Типы товаров</div>
            </a>
            <a href="{{ route('admin.price-rules.index') }}" 
               class="p-4 bg-yellow-50 hover:bg-yellow-100 rounded-lg border border-yellow-200 transition">
                <div class="text-2xl font-bold text-yellow-600">{{ $total['priceRules'] }}</div>
                <div class="text-sm text-gray-700 mt-1 font-medium">💵 Ценовые правила</div>
            </a>
            <a href="{{ route('admin.users.index') }}" 
               class="p-4 bg-indigo-50 hover:bg-indigo-100 rounded-lg border border-indigo-200 transition">
                <div class="text-2xl font-bold text-indigo-600">{{ $total['users'] }}</div>
                <div class="text-sm text-gray-700 mt-1 font-medium">👤 Пользователи</div>
            </a>
            <a href="{{ route('admin.bookings.index') }}" 
               class="p-4 bg-teal-50 hover:bg-teal-100 rounded-lg border border-teal-200 transition">
                <div class="text-2xl font-bold text-teal-600">{{ $total['bookings'] }}</div>
                <div class="text-sm text-gray-700 mt-1 font-medium">📅 Бронирования</div>
            </a>
        </div>
    </div>

    <!-- Быстрые действия -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('admin.bookings.create') }}" 
           class="bg-blue-500 hover:bg-blue-600 text-white p-6 rounded-lg shadow-md text-center transition">
            <div class="text-3xl mb-2">➕</div>
            <div class="font-semibold text-lg">Новое бронирование</div>
        </a>
        <a href="{{ route('admin.bookings.index') }}" 
           class="bg-purple-500 hover:bg-purple-600 text-gray-700 p-6 rounded-lg shadow-md text-center transition">
            <div class="text-3xl mb-2">📅</div>
            <div class="font-semibold text-lg">Все бронирования</div>
        </a>
        <a href="{{ route('admin.resources.index') }}" 
           class="bg-green-500 hover:bg-green-600 text-white p-6 rounded-lg shadow-md text-center transition">
            <div class="text-3xl mb-2">🎱</div>
            <div class="font-semibold text-lg">Управление столами</div>
        </a>
    </div>
</div>