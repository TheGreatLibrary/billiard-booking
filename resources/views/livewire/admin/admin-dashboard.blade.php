<div>
     <h1 class="text-2xl font-bold mb-6">Общая статистика</h1>

    {{-- Быстрые ссылки на основные разделы --}}
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-8">
        <a href="{{ route('admin.payments.index') }}" 
           class="block bg-white p-4 rounded shadow hover:bg-gray-50 transition">
            <div class="text-gray-500 text-sm">Платежей</div>
            <div class="text-2xl font-bold">{{ $total['payments'] }}</div>
        </a>

        <a href="{{ route('admin.orders.index') }}" 
           class="block bg-white p-4 rounded shadow hover:bg-gray-50 transition">
            <div class="text-gray-500 text-sm">Заказов</div>
            <div class="text-2xl font-bold">{{ $total['orders'] }}</div>
        </a>

        <a href="{{ route('admin.bookings.index') }}" 
           class="block bg-white p-4 rounded shadow hover:bg-gray-50 transition">
            <div class="text-gray-500 text-sm">Бронирований</div>
            <div class="text-2xl font-bold">{{ $total['bookings'] }}</div>
        </a>

        <a href="{{ route('admin.users.index') }}" 
           class="block bg-white p-4 rounded shadow hover:bg-gray-50 transition">
            <div class="text-gray-500 text-sm">Пользователей</div>
            <div class="text-2xl font-bold">{{ $total['users'] }}</div>
        </a>

        <a href="{{ route('admin.product-types.index') }}" 
           class="block bg-white p-4 rounded shadow hover:bg-gray-50 transition">
            <div class="text-gray-500 text-sm">Типы товаров</div>
            <div class="text-2xl font-bold">{{ $total['productTypes'] }}</div>
        </a>

        <a href="{{ route('admin.product-models.index') }}" 
           class="block bg-white p-4 rounded shadow hover:bg-gray-50 transition">
            <div class="text-gray-500 text-sm">Модели товаров</div>
            <div class="text-2xl font-bold">{{ $total['productModels'] }}</div>
        </a>

        <a href="{{ route('admin.places.index') }}" 
           class="block bg-white p-4 rounded shadow hover:bg-gray-50 transition">
            <div class="text-gray-500 text-sm">Места</div>
            <div class="text-2xl font-bold">{{ $total['places'] }}</div>
        </a>

        <a href="{{ route('admin.zones.index') }}" 
           class="block bg-white p-4 rounded shadow hover:bg-gray-50 transition">
            <div class="text-gray-500 text-sm">Зоны</div>
            <div class="text-2xl font-bold">{{ $total['zones'] }}</div>
        </a>

        <a href="{{ route('admin.price-rules.index') }}" 
           class="block bg-white p-4 rounded shadow hover:bg-gray-50 transition">
            <div class="text-gray-500 text-sm">Правила цен</div>
            <div class="text-2xl font-bold">{{ $total['priceRules'] }}</div>
        </a>

        <a href="{{ route('admin.resources.index') }}" 
           class="block bg-white p-4 rounded shadow hover:bg-gray-50 transition">
            <div class="text-gray-500 text-sm">Ресурсы (столы)</div>
            <div class="text-2xl font-bold">{{ $total['resources'] }}</div>
        </a>
    </div>

    {{-- Платежи по месяцам --}}
    <div class="bg-white shadow rounded p-4">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Платежи по месяцам</h2>
            <button wire:click="loadStatistics" 
                    class="text-sm text-blue-600 hover:text-blue-800"
                    wire:loading.attr="disabled">
                <span wire:loading.remove>🔄 Обновить</span>
                <span wire:loading>⏳ Загрузка...</span>
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2 text-left">Месяц</th>
                        <th class="px-4 py-2 text-center">Количество</th>
                        <th class="px-4 py-2 text-right">Сумма</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($monthly as $month => $data)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $month }}</td>
                            <td class="px-4 py-2 text-center">{{ $data->count }}</td>
                            <td class="px-4 py-2 text-right font-semibold">
                                {{ number_format($data->amount, 2, ',', ' ') }} ₽
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-2 text-center text-gray-500">
                                Нет данных
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($monthly->count() > 0)
                    <tfoot class="bg-gray-50 font-semibold">
                        <tr class="border-t-2">
                            <td class="px-4 py-2">ИТОГО</td>
                            <td class="px-4 py-2 text-center">
                                {{ $monthly->sum('count') }}
                            </td>
                            <td class="px-4 py-2 text-right">
                                {{ number_format($monthly->sum('amount'), 2, ',', ' ') }} ₽
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
