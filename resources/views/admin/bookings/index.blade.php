@extends('admin.layout.app')

@section('title', 'Управление бронированиями')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Бронирования</h1>
    <a href="{{ route('admin.bookings.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
        ➕ Добавить бронирование
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="p-6">
        @if($bookings->count() > 0)
        <table class="w-full">
            <thead>
                <tr class="border-b">
                    <th class="text-left p-3">ID</th>
                    <th class="text-left p-3">Пользователь</th>
                    <th class="text-left p-3">Место</th>
                    <th class="text-left p-3">Начало</th>
                    <th class="text-left p-3">Конец</th>
                    <th class="text-left p-3">Статус</th>
                    <th class="text-left p-3">Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $booking)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3">{{ $booking->id }}</td>
                    <td class="p-3">{{ $booking->user->name ?? 'N/A' }}</td>
                    <td class="p-3">{{ $booking->place->name ?? 'N/A' }}</td>
                    <td class="p-3">{{ $booking->start_time->format('d.m.Y H:i') }}</td>
                    <td class="p-3">{{ $booking->end_time->format('d.m.Y H:i') }}</td>
                    <td class="p-3">
                        <span class="px-2 py-1 rounded text-xs 
                            {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $booking->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $booking->status === 'canceled' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ $booking->status }}
                        </span>
                    </td>
                    <td class="p-3">
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.bookings.show', $booking) }}" class="text-blue-500 hover:text-blue-700">👁️</a>
                            <a href="{{ route('admin.bookings.edit', $booking) }}" class="text-green-500 hover:text-green-700">✏️</a>
                            <form action="{{ route('admin.bookings.destroy', $booking) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('Удалить бронирование?')">🗑️</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="mt-4">
            {{ $bookings->links() }}
        </div>
        @else
        <div class="text-center py-8">
            <p class="text-gray-500">Бронирования не найдены</p>
        </div>
        @endif
    </div>
</div>
@endsection