<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новое бронирование - Бильярд</title>
   @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <a href="{{ route('bookings.index') }}" class="text-xl font-bold hover:underline">← Назад к бронированиям</a>
                <span>Новое бронирование</span>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8 max-w-2xl">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Забронировать стол</h2>

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('bookings.store') }}" method="POST">
                @csrf
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Выберите стол *</label>
                        <select name="place_id" required
                                class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                            <option value="">-- Выберите стол --</option>
                            @foreach($places as $place)
                                <option value="{{ $place->id }}">{{ $place->name }} - {{ $place->description ?? 'Стол для бильярда' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Начало *</label>
                            <input type="datetime-local" name="start_time" required
                                   min="{{ now()->format('Y-m-d\TH:i') }}"
                                   class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Окончание *</label>
                            <input type="datetime-local" name="end_time" required
                                   class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Дополнительная информация</label>
                        <textarea name="notes" rows="3" placeholder="Ваши пожелания или комментарии..."
                                  class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"></textarea>
                    </div>
                </div>

                <div class="mt-6 flex space-x-4">
                    <button type="submit" 
                            class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium transition duration-200">
                        🎯 Забронировать
                    </button>
                    <a href="{{ route('bookings.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium transition duration-200">
                        Отмена
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>