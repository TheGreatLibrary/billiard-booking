@extends('admin.layout.app')

@section('title', 'Новый заказ')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Новый заказ</h1>
        <p class="text-gray-600">Создание нового заказа</p>
    </div>
    <a href="{{ route('admin.orders.index') }}" 
       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
        ← Назад к списку
    </a>
</div>

<div class="max-w-4xl">
    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('admin.orders.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Пользователь -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Клиент *</label>
                    <select name="user_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                        <option value="">-- Выберите клиента --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Сумма -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Сумма заказа *</label>
                    <input type="number" name="total_amount" step="0.01" min="0"
                           value="{{ old('total_amount') }}" placeholder="0.00"
                           required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                    @error('total_amount') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Статус -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Статус *</label>
                    <select name="status" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Ожидание</option>
                        <option value="processing" {{ old('status') == 'processing' ? 'selected' : '' }}>В обработке</option>
                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Завершено</option>
                        <option value="canceled" {{ old('status') == 'canceled' ? 'selected' : '' }}>Отменено</option>
                    </select>
                    @error('status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Заметки -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Заметки</label>
                    <textarea name="notes" rows="3" 
                              placeholder="Дополнительная информация о заказе..."
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">{{ old('notes') }}</textarea>
                    @error('notes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Позиции заказа (упрощенная версия) -->
            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <h3 class="text-lg font-semibold mb-3">📦 Позиции заказа</h3>
                <div class="text-sm text-gray-600 mb-3">
                    После создания заказа вы сможете добавить товары и услуги в разделе редактирования.
                </div>
                <div class="space-y-2">
                    <div class="flex items-center justify-between p-2 bg-white rounded">
                        <span>Услуга будет добавлена позже</span>
                        <span class="text-gray-500">—</span>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex space-x-3">
                <button type="submit" 
                        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg font-medium">
                    💾 Создать заказ
                </button>
                <a href="{{ route('admin.orders.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-medium">
                    Отмена
                </a>
            </div>
        </form>
    </div>
</div>
@endsection