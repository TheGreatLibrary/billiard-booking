@extends('admin.layout.app')

@section('title', 'Редактирование бронирования #' . $booking->id)

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Редактирование бронирования #{{ $booking->id }}</h1>
        <p class="text-gray-600">Изменение данных бронирования</p>
    </div>
    <a href="{{ route('admin.bookings.show', $booking) }}" 
       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
        ← Назад к просмотру
    </a>
</div>

<div class="max-w-4xl">
    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('admin.bookings.update', $booking) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Пользователь -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Пользователь *</label>
                    <select name="user_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" 
                                {{ $booking->user_id == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Стол -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Стол *</label>
                    <select name="place_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                        @foreach($places as $place)
                            <option value="{{ $place->id }}" 
                                {{ $booking->place_id == $place->id ? 'selected' : '' }}>
                                {{ $place->name }} - {{ $place->description ?? 'Стол для бильярда' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Дата и время -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Начало *</label>
                    <input type="datetime-local" name="start_time" 
                           value="{{ $booking->start_time->format('Y-m-d\TH:i') }}"
                           required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Окончание *</label>
                    <input type="datetime-local" name="end_time" 
                           value="{{ $booking->end_time->format('Y-m-d\TH:i') }}"
                           required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                </div>

                <!-- Статус -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Статус *</label>
                    <select name="status" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                        <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>Ожидание</option>
                        <option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>Подтверждено</option>
                        <option value="canceled" {{ $booking->status == 'canceled' ? 'selected' : '' }}>Отменено</option>
                        <option value="completed" {{ $booking->status == 'completed' ? 'selected' : '' }}>Завершено</option>
                    </select>
                </div>

                <!-- Заметки -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Заметки</label>
                    <textarea name="notes" rows="3" 
                              placeholder="Дополнительная информация..."
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">{{ $booking->notes }}</textarea>
                </div>
            </div>

            <div class="mt-8 flex space-x-3">
                <button type="submit" 
                        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg font-medium">
                    💾 Сохранить изменения
                </button>
                <a href="{{ route('admin.bookings.show', $booking) }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-medium">
                    Отмена
                </a>
            </div>
        </form>
    </div>
</div>
@endsection