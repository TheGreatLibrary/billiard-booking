@extends('admin.layout.app')

@section('title', 'Новый платеж')

@section('content')
<h1 class="text-2xl font-bold mb-6">➕ Новый платеж</h1>

<form action="{{ route('admin.payments.store') }}" method="POST" class="bg-white shadow rounded p-6 space-y-4">
    @csrf
    <div>
        <label class="block text-sm font-medium">Клиент</label>
        <select name="user_id" class="w-full border rounded px-3 py-2">
            @foreach($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium">Заказ (необязательно)</label>
        <select name="order_id" class="w-full border rounded px-3 py-2">
            <option value="">Без заказа</option>
            @foreach($orders as $order)
                <option value="{{ $order->id }}">#{{ $order->id }} — {{ number_format($order->total_amount,2) }} ₽</option>
            @endforeach
        </select>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium">Сумма</label>
            <input type="number" step="0.01" name="amount" class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium">Метод оплаты</label>
            <select name="payment_method" class="w-full border rounded px-3 py-2">
                <option value="cash">Наличные</option>
                <option value="card">Карта</option>
                <option value="online">Онлайн</option>
                <option value="transfer">Перевод</option>
            </select>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium">Статус</label>
            <select name="status" class="w-full border rounded px-3 py-2">
                <option value="pending">Ожидание</option>
                <option value="completed">Завершено</option>
                <option value="failed">Ошибка</option>
                <option value="refunded">Возврат</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium">ID транзакции</label>
            <input type="text" name="transaction_id" class="w-full border rounded px-3 py-2">
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium">Заметки</label>
        <textarea name="notes" rows="3" class="w-full border rounded px-3 py-2"></textarea>
    </div>

    <div class="flex justify-end space-x-2">
        <a href="{{ route('admin.payments.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">Отмена</a>
        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Создать</button>
    </div>
</form>
@endsection
