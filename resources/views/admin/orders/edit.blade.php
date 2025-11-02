@extends('admin.layout.app')

@section('title', 'Редактирование заказа #' . $order->id)

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Редактирование заказа #{{ $order->id }}</h1>
        <p class="text-gray-600">Изменение данных заказа</p>
    </div>
    <a href="{{ route('admin.orders.show', $order) }}" 
       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
        ← Назад к просмотру
    </a>
</div>

<div class="max-w-4xl">
    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('admin.orders.update', $order) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Пользователь -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Клиент *</label>
                    <select name="user_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" 
                                {{ $order->user_id == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Сумма -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Сумма заказа *</label>
                    <input type="number" name="total_amount" step="0.01" min="0"
                           value="{{ $order->total_amount }}" placeholder="0.00"
                           required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                </div>

                <!-- Статус -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Статус *</label>
                    <select name="status" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Ожидание</option>
                        <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>В обработке</option>
                        <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Завершено</option>
                        <option value="canceled" {{ $order->status == 'canceled' ? 'selected' : '' }}>Отменено</option>
                    </select>
                </div>

                <!-- Заметки -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Заметки</label>
                    <textarea name="notes" rows="3" 
                              placeholder="Дополнительная информация о заказе..."
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">{{ $order->notes }}</textarea>
                </div>
            </div>

            <!-- Информация о заказе -->
            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <h3 class="text-lg font-semibold mb-3">ℹ️ Информация о заказе</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600">Дата создания:</span>
                        <span class="font-medium">{{ optional($order->created_at)->format('d.m.Y H:i') ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Последнее обновление:</span>
                        <span class="font-medium">{{ optional($order->updated_at)->format('d.m.Y H:i') ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex space-x-3">
                <button type="submit" 
                        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg font-medium">
                    💾 Сохранить изменения
                </button>
                <a href="{{ route('admin.orders.show', $order) }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-medium">
                    Отмена
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
