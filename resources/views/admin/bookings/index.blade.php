@extends('admin.layout.app')

@section('title', 'Управление бронированиями')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Бронирования</h1>
        <p class="text-gray-600">Все бронирования системы</p>
    </div>
    <a href="{{ route('admin.bookings.create') }}" 
       class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center">
        ➕ Новое бронирование
    </a>
</div>

<!-- Фильтры -->
<div class="bg-white rounded-lg shadow-md p-4 mb-6">
    <div class="flex flex-wrap gap-4">
        <select class="border rounded px-3 py-2">
            <option>Все статусы</option>
            <option>Ожидание</option>
            <option>Подтверждено</option>
            <option>Отменено</option>
        </select>
        <input type="date" class="border rounded px-3 py-2" placeholder="Дата">
        <button class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">Фильтр</button>
    </div>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    @if($bookings->count() > 0)
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Пользователь</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Стол</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Время</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Статус</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Действия</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($bookings as $booking)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">#{{ $booking->id }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $booking->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $booking->user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-900">{{ $booking->place->name ?? 'N/A' }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $booking->starts_at}}</div>
                        <div class="text-sm text-gray-500">{{ $booking->ends_at}}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            @if($booking->status === 'confirmed') bg-green-100 text-green-800
                            @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($booking->status === 'canceled') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ $booking->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.bookings.show', $booking) }}" 
                               class="text-blue-600 hover:text-blue-900">Просмотр</a>
                            <a href="{{ route('admin.bookings.edit', $booking) }}" 
                               class="text-green-600 hover:text-green-900">Изменить</a>
                            <form action="{{ route('admin.bookings.destroy', $booking) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-red-600 hover:text-red-900"
                                        onclick="return confirm('Удалить бронирование?')">
                                    Удалить
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="bg-white px-6 py-3 border-t border-gray-200">
            {{ $bookings->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <div class="text-4xl mb-4">📅</div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Бронирования не найдены</h3>
            <p class="text-gray-500 mb-4">Создайте первое бронирование</p>
            <a href="{{ route('admin.bookings.create') }}" 
               class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                Создать бронирование
            </a>
        </div>
    @endif
</div>
@endsection