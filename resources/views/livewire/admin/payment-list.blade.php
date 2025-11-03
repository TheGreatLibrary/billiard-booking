<div>
     <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold">Платежи</h1>
            <p class="text-gray-600">Все финансовые операции</p>
        </div>
        <a href="{{ route('admin.payments.statistics') }}" 
           class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
            📊 Статистика
        </a>
    </div>

    <!-- Фильтры -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <div class="flex flex-wrap gap-4">
            <input type="text" 
                   wire:model.live.debounce.300ms="search" 
                   placeholder="Поиск по клиенту..."
                   class="border rounded px-3 py-2 flex-1">
            
            <select wire:model.live="statusFilter" class="border rounded px-3 py-2">
                <option value="">Все статусы</option>
                <option value="pending">Ожидание</option>
                <option value="paid">Оплачено</option>
                <option value="failed">Ошибка</option>
                <option value="refunded">Возврат</option>
            </select>

            <select wire:model.live="methodFilter" class="border rounded px-3 py-2">
                <option value="">Все методы</option>
                <option value="cash">Наличные</option>
                <option value="card">Карта</option>
                <option value="online">Онлайн</option>
            </select>

            <span wire:loading class="text-gray-500 self-center">🔄 Загрузка...</span>
        </div>
    </div>

    <!-- Статистика -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-2xl font-bold text-blue-600">{{ $stats['total'] }}</div>
            <div class="text-sm text-gray-600">Всего</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-2xl font-bold text-green-600">{{ $stats['paid'] }}</div>
            <div class="text-sm text-gray-600">Оплачено</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</div>
            <div class="text-sm text-gray-600">Ожидание</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-2xl font-bold text-red-600">{{ $stats['failed'] }}</div>
            <div class="text-sm text-gray-600">Ошибки</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-2xl font-bold text-purple-600">{{ number_format($stats['total_amount'], 2) }} ₽</div>
            <div class="text-sm text-gray-600">Сумма</div>
        </div>
    </div>

    <!-- Таблица -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        @if($payments->count() > 0)
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th wire:click="sortByColumn('id')" 
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer hover:bg-gray-100">
                            ID @if($sortBy === 'id') {{ $sortDirection === 'asc' ? '↑' : '↓' }} @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Заказ</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Клиент</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Сумма</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Метод</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Статус</th>
                        <th wire:click="sortByColumn('paid_at')" 
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer hover:bg-gray-100">
                            Дата @if($sortBy === 'paid_at') {{ $sortDirection === 'asc' ? '↑' : '↓' }} @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Действия</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($payments as $payment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium">#{{ $payment->id }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.orders.show', $payment->order_id) }}" 
                               class="text-blue-600 hover:underline">
                                #{{ $payment->order_id }}
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm">{{ $payment->order->user->name }}</div>
                            <div class="text-sm text-gray-500">{{ $payment->order->user->email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium">{{ number_format($payment->amount, 2) }} ₽</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm">
                                @if($payment->method === 'cash') 💵 Наличные
                                @elseif($payment->method === 'card') 💳 Карта
                                @elseif($payment->method === 'online') 🌐 Онлайн
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 text-xs font-semibold rounded-full
                                @if($payment->status === 'paid') bg-green-100 text-green-800
                                @elseif($payment->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($payment->status === 'failed') bg-red-100 text-red-800
                                @elseif($payment->status === 'refunded') bg-purple-100 text-purple-800
                                @endif">
                                {{ $payment->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm">
                                {{ $payment->paid_at ? $payment->paid_at->format('d.m.Y H:i') : '—' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.payments.show', $payment) }}" 
                                   class="text-blue-600 hover:text-blue-900">👁️</a>
                                <button wire:click="deletePayment({{ $payment->id }})" 
                                        wire:confirm="Удалить платёж?"
                                        class="text-red-600 hover:text-red-900">🗑️</button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="p-4">{{ $payments->links() }}</div>
        @else
            <div class="text-center py-12">
                <div class="text-4xl mb-4">💳</div>
                <h3 class="text-lg font-medium mb-2">Платежей пока нет</h3>
                <p class="text-gray-500">Платежи создаются при оплате заказов</p>
            </div>
        @endif
    </div>
</div>
