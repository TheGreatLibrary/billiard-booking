<div>
   <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold">Заказы</h1>
            <p class="text-gray-600">Все заказы создаются через бронирования</p>
        </div>
        <a href="{{ route('admin.bookings.create') }}" 
           class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
            ➕ Новое бронирование
        </a>
    </div>

    <!-- Фильтры -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <div class="flex gap-4">
            <input type="text" 
                   wire:model.live.debounce.300ms="search" 
                   placeholder="Поиск по клиенту..."
                   class="border rounded px-3 py-2 flex-1">
            
            <select wire:model.live="statusFilter" class="border rounded px-3 py-2">
                <option value="">Все статусы</option>
                <option value="pending">Ожидает оплаты</option>
                <option value="paid">Оплачено</option>
                <option value="canceled">Отменено</option>
                <option value="refunded">Возврат</option>
            </select>

            <span wire:loading class="text-gray-500 self-center">🔄 Загрузка...</span>
        </div>
    </div>

    <!-- Статистика -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-2xl font-bold text-blue-600">{{ $stats['total'] }}</div>
            <div class="text-sm text-gray-600">Всего заказов</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</div>
            <div class="text-sm text-gray-600">Ожидает оплаты</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-2xl font-bold text-green-600">{{ $stats['paid'] }}</div>
            <div class="text-sm text-gray-600">Оплачено</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-2xl font-bold text-purple-600">{{ number_format($stats['total_amount'], 2) }} ₽</div>
            <div class="text-sm text-gray-600">Сумма оплат</div>
        </div>
    </div>

    <!-- Таблица -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        @if($orders->count() > 0)
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th wire:click="sortByColumn('id')" 
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer hover:bg-gray-100">
                            ID @if($sortBy === 'id') {{ $sortDirection === 'asc' ? '↑' : '↓' }} @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Бронирование</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Клиент</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Сумма</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Статус</th>
                        <th wire:click="sortByColumn('created_at')" 
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer hover:bg-gray-100">
                            Дата @if($sortBy === 'created_at') {{ $sortDirection === 'asc' ? '↑' : '↓' }} @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Действия</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($orders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium">#{{ $order->id }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.bookings.show', $order->booking_id) }}" 
                               class="text-blue-600 hover:underline">
                                #{{ $order->booking_id }}
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm">{{ $order->user->name }}</div>
                            <div class="text-sm text-gray-500">{{ $order->user->email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium">{{ number_format($order->total_amount, 2) }} ₽</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 text-xs font-semibold rounded-full
                                @if($order->status === 'paid') bg-green-100 text-green-800
                                @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($order->status === 'canceled') bg-gray-100 text-gray-800
                                @elseif($order->status === 'refunded') bg-purple-100 text-purple-800
                                @endif">
                                {{ $order->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm">{{ $order->created_at->format('d.m.Y H:i') }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.orders.show', $order) }}" 
                                   class="text-blue-600 hover:text-blue-900" title="Просмотр">Просмотр</a>
                                
                                @if($order->canPay())
                                    <a href="{{ route('admin.orders.pay', $order) }}" 
                                       class="text-purple-600 hover:text-purple-900" title="Оплатить">Оплатить</a>
                                @endif
                                
                                @if($order->canEdit())
                                    <button wire:click="deleteOrder({{ $order->id }})" 
                                            wire:confirm="Удалить заказ?"
                                            class="text-red-600 hover:text-red-900" title="Удалить">Удалить</button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="p-4">{{ $orders->links() }}</div>
        @else
            <div class="text-center py-12">
                <div class="text-4xl mb-4">🛒</div>
                <h3 class="text-lg font-medium mb-2">Заказов пока нет</h3>
                <p class="text-gray-500 mb-4">Заказы создаются автоматически при бронировании</p>
                <a href="{{ route('admin.bookings.create') }}" 
                   class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                    Создать бронирование
                </a>
            </div>
        @endif
    </div>
</div>
