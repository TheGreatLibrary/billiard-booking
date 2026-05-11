<div>
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Панель управления</h1>
            <p class="text-gray-500 text-sm">Обзор системы бронирования</p>
        </div>
        <button wire:click="loadStatistics" 
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition text-sm font-medium"
                wire:loading.attr="disabled">
            <span wire:loading.remove>Обновить</span>
            <span wire:loading>Загрузка...</span>
        </button>
    </div>

    {{-- Выручка --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Сегодня</p>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($revenue['today'], 0, '', ' ') }} ₽</p>
            <p class="text-xs text-gray-400 mt-1">{{ $revenue['today_count'] }} бронирований</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">За неделю</p>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($revenue['week'], 0, '', ' ') }} ₽</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">За месяц</p>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($revenue['month'], 0, '', ' ') }} ₽</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Всего выручка</p>
            <p class="text-2xl font-bold text-emerald-600">{{ number_format($total['amount'], 0, '', ' ') }} ₽</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Средний чек</p>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($revenue['avg_check'], 0, '', ' ') }} ₽</p>
        </div>
    </div>

    {{-- Основные метрики --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-blue-600 uppercase tracking-wider">Бронирования</p>
                    <p class="text-3xl font-bold text-blue-700 mt-1">{{ $total['bookings'] }}</p>
                </div>
                <div class="text-3xl opacity-50">📅</div>
            </div>
            <p class="text-xs text-blue-500 mt-2">
                <span class="font-semibold">{{ $total['bookings_paid'] }}</span> оплачено · 
                <span class="font-semibold">{{ $total['bookings_pending'] }}</span> ожидает
            </p>
        </div>
        <div class="bg-purple-50 border border-purple-200 rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-purple-600 uppercase tracking-wider">Пользователи</p>
                    <p class="text-3xl font-bold text-purple-700 mt-1">{{ $total['users'] }}</p>
                </div>
                <div class="text-3xl opacity-50">👥</div>
            </div>
        </div>
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-emerald-600 uppercase tracking-wider">Столы</p>
                    <p class="text-3xl font-bold text-emerald-700 mt-1">{{ $total['tables'] }}</p>
                </div>
                <div class="text-3xl opacity-50">🎱</div>
            </div>
            <p class="text-xs text-emerald-500 mt-2">+ {{ $total['equipment'] }} оборудование</p>
        </div>
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-amber-600 uppercase tracking-wider">Заведения</p>
                    <p class="text-3xl font-bold text-amber-700 mt-1">{{ $total['places'] }}</p>
                </div>
                <div class="text-3xl opacity-50">🏢</div>
            </div>
            <p class="text-xs text-amber-500 mt-2">{{ $total['zones'] }} зон · {{ $total['priceRules'] }} правил</p>
        </div>
    </div>

    {{-- График + Популярные столы --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- График выручки --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Динамика выручки</h2>
            @if(count($chartData['labels']) > 0)
                <div style="position:relative;height:220px">
                    <canvas id="revenueChart"></canvas>
                </div>
            @else
                <div class="text-center py-12 text-gray-400">
                    <p class="text-4xl mb-2">📊</p>
                    <p>Нет данных по оплатам</p>
                </div>
            @endif
        </div>

        {{-- Популярные столы --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Популярные столы</h2>
            @if(count($popularTables) > 0)
                <div class="space-y-3">
                    @foreach($popularTables as $i => $table)
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold
                                {{ $i === 0 ? 'bg-amber-100 text-amber-700' : ($i === 1 ? 'bg-gray-100 text-gray-600' : 'bg-orange-50 text-orange-600') }}">
                                {{ $i + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-800 truncate">{{ $table['code'] }}</p>
                                <p class="text-xs text-gray-400">{{ $table['model'] }}</p>
                            </div>
                            <div class="text-sm font-bold text-gray-700">{{ $table['count'] }}</div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-400 text-center py-6">Нет данных</p>
            @endif
        </div>
    </div>

    {{-- Статусы + Загруженность по дням --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- Статусы бронирований --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Статусы бронирований</h2>
            <div class="grid grid-cols-2 gap-3">
                <div class="text-center p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                    <div class="text-2xl font-bold text-yellow-600">{{ $statusStats['pending'] ?? 0 }}</div>
                    <div class="text-xs text-gray-600 mt-1">Ожидание</div>
                </div>
                <div class="text-center p-3 bg-green-50 rounded-lg border border-green-200">
                    <div class="text-2xl font-bold text-green-600">{{ $statusStats['confirmed'] ?? 0 }}</div>
                    <div class="text-xs text-gray-600 mt-1">Подтверждено</div>
                </div>
                <div class="text-center p-3 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="text-2xl font-bold text-blue-600">{{ $statusStats['finished'] ?? 0 }}</div>
                    <div class="text-xs text-gray-600 mt-1">Завершено</div>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="text-2xl font-bold text-gray-500">{{ $statusStats['canceled'] ?? 0 }}</div>
                    <div class="text-xs text-gray-600 mt-1">Отменено</div>
                </div>
            </div>
        </div>

        {{-- Статусы оплаты --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Статусы оплаты</h2>
            <div class="grid grid-cols-2 gap-3">
                <div class="text-center p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                    <div class="text-2xl font-bold text-yellow-600">{{ $paymentStatusStats['pending'] ?? 0 }}</div>
                    <div class="text-xs text-gray-600 mt-1">Ожидает</div>
                </div>
                <div class="text-center p-3 bg-green-50 rounded-lg border border-green-200">
                    <div class="text-2xl font-bold text-green-600">{{ $paymentStatusStats['paid'] ?? 0 }}</div>
                    <div class="text-xs text-gray-600 mt-1">Оплачено</div>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="text-2xl font-bold text-gray-500">{{ $paymentStatusStats['canceled'] ?? 0 }}</div>
                    <div class="text-xs text-gray-600 mt-1">Отменено</div>
                </div>
                <div class="text-center p-3 bg-purple-50 rounded-lg border border-purple-200">
                    <div class="text-2xl font-bold text-purple-600">{{ $paymentStatusStats['refunded'] ?? 0 }}</div>
                    <div class="text-xs text-gray-600 mt-1">Возврат</div>
                </div>
            </div>
        </div>

        {{-- Загруженность по дням недели --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">По дням недели</h2>
            @if(count($weekdayStats) > 0)
                @php $maxDay = max($weekdayStats->toArray()) ?: 1; @endphp
                <div class="space-y-2">
                    @foreach($weekdayStats as $day => $count)
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-medium text-gray-600 w-6">{{ $day }}</span>
                            <div class="flex-1 bg-gray-100 rounded-full h-5 overflow-hidden">
                                <div class="h-full rounded-full transition-all {{ in_array($day, ['Пт', 'Сб']) ? 'bg-emerald-500' : 'bg-blue-400' }}"
                                     style="width: {{ ($count / $maxDay) * 100 }}%"></div>
                            </div>
                            <span class="text-xs font-bold text-gray-700 w-8 text-right">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-400 text-center py-6">Нет данных</p>
            @endif
        </div>
    </div>

    {{-- Последние бронирования --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Последние бронирования</h2>
            <a href="{{ route('admin.bookings.index') }}" class="text-sm text-blue-600 hover:text-blue-800 transition">Все бронирования →</a>
        </div>
        @if(count($recentBookings) > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <th class="pb-3 pr-4">#</th>
                            <th class="pb-3 pr-4">Клиент</th>
                            <th class="pb-3 pr-4">Место</th>
                            <th class="pb-3 pr-4">Сумма</th>
                            <th class="pb-3 pr-4">Статус</th>
                            <th class="pb-3">Дата</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($recentBookings as $b)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="py-3 pr-4 text-sm font-mono text-gray-500">{{ $b->id }}</td>
                                <td class="py-3 pr-4 text-sm font-medium text-gray-800">{{ $b->guest_name ?? $b->user?->name ?? '—' }}</td>
                                <td class="py-3 pr-4 text-sm text-gray-600">{{ $b->place?->name ?? '—' }}</td>
                                <td class="py-3 pr-4 text-sm font-semibold text-gray-800">{{ number_format($b->total_amount ?? 0, 0, '', ' ') }} ₽</td>
                                <td class="py-3 pr-4">
                                    @php
                                        $colors = ['paid'=>'bg-green-100 text-green-700','pending'=>'bg-yellow-100 text-yellow-700','canceled'=>'bg-gray-100 text-gray-600','refunded'=>'bg-purple-100 text-purple-700'];
                                        $labels = ['paid'=>'Оплачено','pending'=>'Ожидает','canceled'=>'Отменено','refunded'=>'Возврат'];
                                    @endphp
                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $colors[$b->payment_status] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ $labels[$b->payment_status] ?? $b->payment_status }}
                                    </span>
                                </td>
                                <td class="py-3 text-sm text-gray-500">{{ $b->created_at?->format('d.m.Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-sm text-gray-400 text-center py-6">Нет бронирований</p>
        @endif
    </div>

    {{-- Справочники --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Справочники</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <a href="{{ route('admin.places.index') }}" class="p-4 bg-blue-50 hover:bg-blue-100 rounded-lg border border-blue-200 transition">
                <div class="text-2xl font-bold text-blue-600">{{ $total['places'] }}</div>
                <div class="text-xs text-gray-600 mt-1 font-medium">Заведения</div>
            </a>
            <a href="{{ route('admin.zones.index') }}" class="p-4 bg-purple-50 hover:bg-purple-100 rounded-lg border border-purple-200 transition">
                <div class="text-2xl font-bold text-purple-600">{{ $total['zones'] }}</div>
                <div class="text-xs text-gray-600 mt-1 font-medium">Зоны</div>
            </a>
            <a href="{{ route('admin.resources.index') }}" class="p-4 bg-emerald-50 hover:bg-emerald-100 rounded-lg border border-emerald-200 transition">
                <div class="text-2xl font-bold text-emerald-600">{{ $total['resources'] }}</div>
                <div class="text-xs text-gray-600 mt-1 font-medium">Ресурсы</div>
            </a>
            <a href="{{ route('admin.product-models.index') }}" class="p-4 bg-orange-50 hover:bg-orange-100 rounded-lg border border-orange-200 transition">
                <div class="text-2xl font-bold text-orange-600">{{ $total['productModels'] }}</div>
                <div class="text-xs text-gray-600 mt-1 font-medium">Модели</div>
            </a>
            <a href="{{ route('admin.product-types.index') }}" class="p-4 bg-pink-50 hover:bg-pink-100 rounded-lg border border-pink-200 transition">
                <div class="text-2xl font-bold text-pink-600">{{ $total['productTypes'] }}</div>
                <div class="text-xs text-gray-600 mt-1 font-medium">Типы</div>
            </a>
            <a href="{{ route('admin.price-rules.index') }}" class="p-4 bg-amber-50 hover:bg-amber-100 rounded-lg border border-amber-200 transition">
                <div class="text-2xl font-bold text-amber-600">{{ $total['priceRules'] }}</div>
                <div class="text-xs text-gray-600 mt-1 font-medium">Ценовые правила</div>
            </a>
            <a href="{{ route('admin.users.index') }}" class="p-4 bg-indigo-50 hover:bg-indigo-100 rounded-lg border border-indigo-200 transition">
                <div class="text-2xl font-bold text-indigo-600">{{ $total['users'] }}</div>
                <div class="text-xs text-gray-600 mt-1 font-medium">Пользователи</div>
            </a>
            <a href="{{ route('admin.bookings.index') }}" class="p-4 bg-teal-50 hover:bg-teal-100 rounded-lg border border-teal-200 transition">
                <div class="text-2xl font-bold text-teal-600">{{ $total['bookings'] }}</div>
                <div class="text-xs text-gray-600 mt-1 font-medium">Бронирования</div>
            </a>
        </div>
    </div>
</div>

@if(count($chartData['labels']) > 0)
<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('revenueChart');
    if (!ctx || typeof Chart === 'undefined') return;
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($chartData['labels']),
            datasets: [
                {
                    label: 'Выручка (₽)',
                    data: @json($chartData['amounts']),
                    backgroundColor: 'rgba(16, 185, 129, 0.7)',
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 1,
                    borderRadius: 6,
                    yAxisID: 'y'
                },
                {
                    label: 'Кол-во броней',
                    data: @json($chartData['counts']),
                    type: 'line',
                    borderColor: 'rgba(99, 102, 241, 1)',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    borderWidth: 2,
                    pointRadius: 4,
                    pointBackgroundColor: 'rgba(99, 102, 241, 1)',
                    fill: true,
                    tension: 0.3,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            plugins: {
                legend: { position: 'top', labels: { usePointStyle: true, padding: 16, font: { size: 12 } } }
            },
            scales: {
                y: {
                    position: 'left',
                    beginAtZero: true,
                    ticks: { callback: function(v) { return v.toLocaleString('ru') + ' ₽'; }, font: { size: 11 } },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                y1: {
                    position: 'right',
                    beginAtZero: true,
                    grid: { drawOnChartArea: false },
                    ticks: { font: { size: 11 } }
                },
                x: {
                    ticks: { font: { size: 11 } },
                    grid: { display: false }
                }
            }
        }
    });
});
</script>
@endif