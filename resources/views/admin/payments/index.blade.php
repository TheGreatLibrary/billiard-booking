@extends('admin.layout.app')

@section('title', 'Управление платежами')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Платежи</h1>
        <p class="text-gray-600">Финансовые операции системы</p>
    </div>
    <div class="flex space-x-2">
        <a href="{{ route('admin.payments.create') }}" 
           class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center">
             Новый платеж
        </a>
        <a href="{{ route('admin.payments.statistics') }}" 
           class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg flex items-center">
             Статистика
        </a>
    </div>
</div>

<!-- Статистика платежей -->
<div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-2xl font-bold text-blue-600">{{ $stats['total'] }}</div>
        <div class="text-sm text-gray-600">Всего платежей</div>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-2xl font-bold text-green-600">{{ $stats['completed'] }}</div>
        <div class="text-sm text-gray-600">Завершено</div>
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
        <div class="text-sm text-gray-600">Общая сумма</div>
    </div>
</div>

<!-- Фильтры -->
<div class="bg-white rounded-lg shadow-md p-4 mb-6">
    <div class="flex flex-wrap gap-4 items-center">
        <select class="border rounded px-3 py-2 text-sm">
            <option>Все статусы</option>
            <option>Завершено</option>
            <option>Ожидание</option>
            <option>Ошибка</option>
        </select>
        <input type="date" class="border rounded px-3 py-2 text-sm" placeholder="Дата">
        <select class="border rounded px-3 py-2 text-sm">
            <option>Все методы</option>
            <option>Наличные</option>
            <option>Карта</option>
            <option>Онлайн</option>
        </select>
        <button class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded text-sm">Фильтр</button>
    </div>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    @if($payments->count() > 0)
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Клиент</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Заказ</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Сумма</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Метод</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Статус</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Дата</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Действия</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($payments as $payment)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">#{{ $payment->id }}</div>
                        @if($payment->transaction_id)
                        <div class="text-xs text-gray-500">{{ $payment->transaction_id }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">{{ $payment->user->name }}</div>
                        <div class="text-sm text-gray-500">{{ $payment->user->email }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($payment->order)
                        <div class="text-sm text-gray-900">Заказ #{{ $payment->order->id }}</div>
                        <div class="text-xs text-gray-500">{{ number_format($payment->order->total_amount, 2) }} ₽</div>
                        @else
                        <span class="text-xs text-gray-400">Без заказа</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ number_format($payment->amount, 2) }} ₽</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-900">
                            @if($payment->payment_method === 'cash') 💵 Наличные
                            @elseif($payment->payment_method === 'card') 💳 Карта
                            @elseif($payment->payment_method === 'online') 🌐 Онлайн
                            @elseif($payment->payment_method === 'transfer') 🏦 Перевод
                            @else {{ $payment->payment_method }}
                            @endif
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            @if($payment->status === 'completed') bg-green-100 text-green-800
                            @elseif($payment->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($payment->status === 'failed') bg-red-100 text-red-800
                            @elseif($payment->status === 'refunded') bg-purple-100 text-purple-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ $payment->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $payment->created_at->format('d.m.Y') }}</div>
                        <div class="text-sm text-gray-500">{{ $payment->created_at->format('H:i') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.payments.show', $payment) }}" 
                               class="text-blue-600 hover:text-blue-900" title="Просмотр">
                                👁️
                            </a>
                            <a href="{{ route('admin.payments.edit', $payment) }}" 
                               class="text-green-600 hover:text-green-900" title="Редактировать">
                                ✏️
                            </a>
                            <form action="{{ route('admin.payments.destroy', $payment) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-red-600 hover:text-red-900"
                                        onclick="return confirm('Удалить платеж?')"
                                        title="Удалить">
                                    🗑️
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="bg-white px-6 py-3 border-t border-gray-200">
            {{ $payments->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <div class="text-4xl mb-4">💳</div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Платежи не найдены</h3>
            <p class="text-gray-500 mb-4">Создайте первый платеж</p>
            <a href="{{ route('admin.payments.create') }}" 
               class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                Создать платеж
            </a>
        </div>
    @endif
</div>
@endsection