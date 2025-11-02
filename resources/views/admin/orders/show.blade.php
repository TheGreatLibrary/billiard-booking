@extends('admin.layout.app')

@section('title', 'Заказ #' . $order->id)

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Заказ #{{ $order->id }}</h1>
        <p class="text-gray-600">Детальная информация о заказе</p>
    </div>
    <div class="flex space-x-2">
        <a href="{{ route('admin.orders.edit', $order) }}" 
           class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
            Редактировать
        </a>
        <a href="{{ route('admin.orders.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
            Назад
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Основная информация -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Информация о заказе -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4">Информация о заказе</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600">ID заказа</label>
                    <p class="mt-1 text-lg font-semibold text-gray-900">#{{ $order->id }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Статус</label>
                    <span class="mt-1 inline-flex px-3 py-1 text-sm font-semibold rounded-full
                        @if($order->status === 'completed') bg-green-100 text-green-800
                        @elseif($order->status === 'processing') bg-yellow-100 text-yellow-800
                        @elseif($order->status === 'pending') bg-blue-100 text-blue-800
                        @elseif($order->status === 'canceled') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ $order->status }}
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Общая сумма</label>
                    <p class="mt-1 text-xl font-bold text-gray-900">{{ number_format($order->total_amount ?? 0, 2) }} ₽</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Дата создания</label>
                    <p class="mt-1 text-sm text-gray-900">{{ optional($order->created_at)->format('d.m.Y H:i') ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Информация о пользователе -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4">Информация о клиенте</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600">Имя</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $order->user->name ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Email</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $order->user->email ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Телефон</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $order->user->phone ?? 'Не указан' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Дата регистрации</label>
                    <p class="mt-1 text-sm text-gray-900">{{ optional($order->user->created_at)->format('d.m.Y') ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Позиции заказа -->
        @if($order->items && $order->items->count() > 0)
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4">Позиции заказа</h2>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Товар/Услуга</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Количество</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Цена</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Сумма</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($order->items as $item)
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $item->product_name ?? 'Товар' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $item->quantity ?? 0 }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ number_format($item->price ?? 0, 2) }} ₽</td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                {{ number_format(($item->quantity ?? 0) * ($item->price ?? 0), 2) }} ₽
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="3" class="px-4 py-3 text-sm font-medium text-gray-900 text-right">Итого:</td>
                            <td class="px-4 py-3 text-sm font-bold text-gray-900">{{ number_format($order->total_amount ?? 0, 2) }} ₽</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        @endif
    </div>

    <!-- Боковая панель -->
    <div class="space-y-6">
        <!-- Действия -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4">Действия</h2>
            <div class="flex space-x-2 pt-2">
                <a href="{{ route('admin.orders.edit', $order) }}" 
                   class="flex-1 bg-blue-500 hover:bg-blue-600 text-white text-center py-2 rounded text-sm">
                    Изменить
                </a>
                <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="w-full bg-red-500 hover:bg-red-600 text-white py-2 rounded text-sm"
                            onclick="return confirm('Удалить заказ?')">
                        Удалить
                    </button>
                </form>
            </div>
        </div>

        <!-- Дополнительная информация -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4">Дополнительно</h2>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-600">Создан</label>
                    <p class="text-sm text-gray-900">{{ optional($order->created_at)->format('d.m.Y H:i') ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Обновлен</label>
                    <p class="text-sm text-gray-900">{{ optional($order->updated_at)->format('d.m.Y H:i') ?? '-' }}</p>
                </div>
                @if($order->notes)
                <div>
                    <label class="block text-sm font-medium text-gray-600">Заметки</label>
                    <p class="text-sm text-gray-900 mt-1 p-2 bg-gray-50 rounded">{{ $order->notes }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
