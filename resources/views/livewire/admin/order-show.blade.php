<div>
     <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold">Заказ #{{ $order->id }}</h1>
            <p class="text-gray-600">Детальная информация</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin.orders.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                ← Назад
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <!-- Информация о заказе -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold mb-4">📋 Информация о заказе</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-gray-600">ID заказа</label>
                        <p class="text-lg font-semibold">#{{ $order->id }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">Сумма</label>
                        <p class="text-xl font-bold text-green-600">{{ number_format($order->total_amount, 2) }} ₽</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">Дата создания</label>
                        <p class="text-sm">{{ $order->created_at->format('d.m.Y H:i') }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">Бронирование</label>
                        <p class="text-sm">
                            <a href="{{ route('admin.bookings.show', $order->booking_id) }}" 
                               class="text-blue-600 hover:underline">
                                #{{ $order->booking_id }}
                            </a>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Клиент -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold mb-4">👤 Клиент</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-gray-600">Имя</label>
                        <p>{{ $order->user->name }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">Email</label>
                        <p>{{ $order->user->email }}</p>
                    </div>
                    @if($order->user->phone)
                    <div>
                        <label class="text-sm text-gray-600">Телефон</label>
                        <p>{{ $order->user->phone }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Позиции -->
            @if($order->items && $order->items->count() > 0)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold mb-4">📦 Позиции заказа</h2>
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-sm">Наименование</th>
                            <th class="px-4 py-2 text-left text-sm">Кол-во</th>
                            <th class="px-4 py-2 text-left text-sm">Сумма</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr class="border-t">
                            <td class="px-4 py-3">
                                @if($item->type === 'table_time')
                                    Аренда стола
                                @else
                                    {{ $item->productModel->name ?? 'Оборудование' }}
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $item->qty }}</td>
                            <td class="px-4 py-3 font-semibold">{{ number_format($item->amount, 2) }} ₽</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 font-bold">
                        <tr>
                            <td colspan="2" class="px-4 py-3 text-right">ИТОГО:</td>
                            <td class="px-4 py-3">{{ number_format($order->total_amount, 2) }} ₽</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @endif
        </div>

        <!-- Боковая панель -->
        <div class="space-y-6">
            <!-- Действия -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold mb-4">⚡ Действия</h2>
                
                <div class="flex flex-col space-y-2">
                    <button wire:click="deleteOrder" 
                            wire:confirm="Удалить заказ?"
                            class="bg-red-500 hover:bg-red-600 text-white py-2 rounded">
                        🗑️ Удалить заказ
                    </button>
                </div>
            </div>

            <!-- Дополнительно -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold mb-4">📊 Информация</h2>
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-gray-600">Создан:</span>
                        <p>{{ $order->created_at->format('d.m.Y H:i') }}</p>
                    </div>
                    <div>
                        <span class="text-gray-600">Место:</span>
                        <p>{{ $order->place->name }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
