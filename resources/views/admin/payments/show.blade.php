@extends('admin.layout.app')

@section('title', 'Платеж #' . $payment->id)

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Платеж #{{ $payment->id }}</h1>
        <p class="text-gray-600">Детальная информация о платеже</p>
    </div>
    <div class="flex space-x-2">
        <a href="{{ route('admin.payments.edit', $payment) }}" 
           class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
            ✏️ Редактировать
        </a>
        <a href="{{ route('admin.payments.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
            ← Назад
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Основная информация -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Информация о платеже -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4">💳 Информация о платеже</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600">ID платежа</label>
                    <p class="mt-1 text-lg font-semibold text-gray-900">#{{ $payment->id }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Статус</label>
                    <span class="mt-1 inline-flex px-3 py-1 text-sm font-semibold rounded-full
                        @if($payment->status === 'completed') bg-green-100 text-green-800
                        @elseif($payment->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($payment->status === 'failed') bg-red-100 text-red-800
                        @elseif($payment->status === 'refunded') bg-purple-100 text-purple-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ $payment->status }}
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Сумма</label>
                    <p class="mt-1 text-xl font-bold text-gray-900">{{ number_format($payment->amount, 2) }} ₽</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Метод оплаты</label>
                    <p class="mt-1 text-sm text-gray-900">
                        @if($payment->payment_method === 'cash') 💵 Наличные
                        @elseif($payment->payment_method === 'card') 💳 Карта
                        @elseif($payment->payment_method === 'online') 🌐 Онлайн
                        @elseif($payment->payment_method === 'transfer') 🏦 Перевод
                        @else {{ $payment->payment_method }}
                        @endif
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">ID транзакции</label>
                    <p class="mt-1 text-sm text-gray-900 font-mono">{{ $payment->transaction_id ?? 'Не указан' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Дата создания</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $payment->created_at->format('d.m.Y H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Информация о клиенте -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4">👤 Информация о клиенте</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600">Имя</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $payment->user->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Email</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $payment->user->email }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Телефон</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $payment->user->phone ?? 'Не указан' }}</p>
                </div>
            </div>
        </div>

        <!-- Информация о заказе -->
        @if($payment->order)
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4">🛒 Связанный заказ</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600">ID заказа</label>
                    <a href="{{ route('admin.orders.show', $payment->order) }}" 
                       class="mt-1 text-sm text-blue-600 hover:text-blue-900">
                        Заказ #{{ $payment->order->id }}
                    </a>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Сумма заказа</label>
                    <p class="mt-1 text-sm text-gray-900">{{ number_format($payment->order->total_amount, 2) }} ₽</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Статус заказа</label>
                    <span class="mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full
                        @if($payment->order->status === 'completed') bg-green-100 text-green-800
                        @elseif($payment->order->status === 'processing') bg-yellow-100 text-yellow-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ $payment->order->status }}
                    </span>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Боковая панель -->
    <div class="space-y-6">
        <!-- Действия -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4">⚡ Действия</h2>
            <div class="space-y-3">
                <form action="{{ route('admin.payments.change-status', $payment) }}" method="POST">
                    @csrf
                    <label class="block text-sm font-medium text-gray-700 mb-2">Изменить статус</label>
                    <select name="status" onchange="this.form.submit()" 
                            class="w-full border rounded px-3 py-2 text-sm">
                        <option value="pending" {{ $payment->status === 'pending' ? 'selected' : '' }}>Ожидание</option>
                        <option value="completed" {{ $payment->status === 'completed' ? 'selected' : '' }}>Завершено</option>
                        <option value="failed" {{ $payment->status === 'failed' ? 'selected' : '' }}>Ошибка</option>
                        <option value="refunded" {{ $payment->status === 'refunded' ? 'selected' : '' }}>Возврат</option>
                    </select>
                </form>
                
                <div class="flex space-x-2 pt-2">
                    <a href="{{ route('admin.payments.edit', $payment) }}" 
                       class="flex-1 bg-blue-500 hover:bg-blue-600 text-white text-center py-2 rounded text-sm">
                        ✏️ Изменить
                    </a>
                    <form action="{{ route('admin.payments.destroy', $payment) }}" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full bg-red-500 hover:bg-red-600 text-white py-2 rounded text-sm"
                                onclick="return confirm('Удалить платеж?')">
                            🗑️ Удалить
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Дополнительная информация -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4">📊 Дополнительно</h2>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-600">Создан</label>
                    <p class="text-sm text-gray-900">{{ $payment->created_at->format('d.m.Y H:i') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Обновлен</label>
                    <p class="text-sm text-gray-900">{{ $payment->updated_at->format('d.m.Y H:i') }}</p>
                </div>
                @if($payment->notes)
                <div>
                    <label class="block text-sm font-medium text-gray-600">Заметки</label>
                    <p class="text-sm text-gray-900 mt-1 p-2 bg-gray-50 rounded">{{ $payment->notes }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection