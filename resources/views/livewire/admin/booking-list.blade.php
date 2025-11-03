<div>
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Бронирования</h1>
            <p class="text-gray-600">Все бронирования системы</p>
        </div>
        <a href="{{ route('admin.bookings.create') }}" 
           class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
            ➕ Новое бронирование
        </a>
    </div>

    <!-- Фильтры -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <div class="flex flex-wrap gap-4">
            <input type="text" 
                   wire:model.live="search" 
                   placeholder="Поиск по имени..."
                   class="border rounded px-3 py-2">
            
            <select wire:model.live="statusFilter" class="border rounded px-3 py-2">
                <option value="">Все статусы</option>
                <option value="pending">Ожидание</option>
                <option value="confirmed">Подтверждено</option>
                <option value="canceled">Отменено</option>
            </select>
        </div>
    </div>

    <!-- Таблица -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        @if($bookings->count() > 0)
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Пользователь</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Место</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Время</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Статус</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Действия</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($bookings as $booking)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">#{{ $booking->id }}</td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium">{{ $booking->user->name }}</div>
                            <div class="text-sm text-gray-500">{{ $booking->user->email }}</div>
                        </td>
                        <td class="px-6 py-4">{{ $booking->place->name }}</td>
                        <td class="px-6 py-4">
                            <div class="text-sm">{{ $booking->starts_at }}</div>
                            <div class="text-sm text-gray-500">{{ $booking->ends_at }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 text-xs font-semibold rounded-full
                                @if($booking->status === 'confirmed') bg-green-100 text-green-800
                                @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ $booking->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.bookings.show', $booking) }}" 
                                   class="text-blue-600 hover:text-blue-900">Просмотр</a>
                                <a href="{{ route('admin.bookings.edit', $booking) }}" 
                                   class="text-green-600 hover:text-green-900">Изменить</a>
                                <button wire:click="deleteBooking({{ $booking->id }})" 
                                        wire:confirm="Удалить бронирование?"
                                        class="text-red-600 hover:text-red-900">
                                    Удалить
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="p-4">
                {{ $bookings->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <div class="text-4xl mb-4">📅</div>
                <h3 class="text-lg font-medium mb-2">Бронирования не найдены</h3>
                <a href="{{ route('admin.bookings.create') }}" 
                   class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                    Создать бронирование
                </a>
            </div>
        @endif
    </div>
</div>
