<div>
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Бронирование #{{ $booking->id }}</h1>
            <p class="text-gray-600">Детали бронирования</p>
        </div>
        <a href="{{ route('admin.bookings.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
            ← Назад к списку
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Основная информация -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Информация о клиенте -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold mb-4">Информация о клиенте</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Имя</p>
                        <p class="font-medium">{{ $booking->getClientName() }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Email</p>
                        <p class="font-medium">{{ $booking->getClientEmail() ?? 'Не указан' }}</p>
                    </div>
                    @if($booking->getClientPhone())
                    <div>
                        <p class="text-sm text-gray-500">Телефон</p>
                        <p class="font-medium">{{ $booking->getClientPhone() }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Забронированный стол -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold mb-4">Забронированный стол</h2>
                
                <div class="border-b pb-4 mb-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-medium text-lg">🎱 {{ $booking->resource->code ?? 'Стол' }}</p>
                            <p class="text-sm text-gray-500">{{ $booking->resource->model->name }}</p>
                            <p class="text-sm text-gray-600 mt-1">
                                Зона: {{ $booking->resource->zone->name ?? 'N/A' }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-green-600">
                                {{ $booking->getTotalAmountFormatted() }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Временные слоты -->
                <div class="mt-4">
                    <p class="text-sm font-medium mb-2">Забронированное время:</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($booking->slots as $slot)
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                                {{ \Carbon\Carbon::parse($slot->slot_datetime)->format('d.m.Y H:i') }}
                            </span>
                        @endforeach
                    </div>
                    <p class="text-sm text-gray-500 mt-2">
                        Всего часов: {{ $booking->slots->count() }}
                    </p>
                </div>
            </div>

            <!-- Дополнительное оборудование -->
            @if($booking->equipment->count() > 0)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold mb-4">Дополнительное оборудование</h2>
                
                @foreach($booking->equipment as $item)
                <div class="flex justify-between items-center py-2 border-b last:border-b-0">
                    <div>
                        <p class="font-medium">{{ $item->productModel->name }}</p>
                        <p class="text-sm text-gray-500">x{{ $item->qty }}</p>
                    </div>
                    <p class="font-semibold">{{ $item->getAmountFormatted() }}</p>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <!-- Боковая панель -->
        <div class="space-y-6">
            <!-- Статус и управление -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold mb-4">Статус</h2>
                
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Статус бронирования</p>
                        <span class="inline-block px-3 py-1 text-sm font-semibold rounded
                            @if($booking->status === 'confirmed') bg-green-100 text-green-800
                            @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($booking->status === 'canceled') bg-red-100 text-red-800
                            @elseif($booking->status === 'finished') bg-blue-100 text-blue-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ $booking->status }}
                        </span>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500 mb-1">Статус оплаты</p>
                        <span class="inline-block px-3 py-1 text-sm font-semibold rounded
                            @if($booking->payment_status === 'paid') bg-green-100 text-green-800
                            @elseif($booking->payment_status === 'pending') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ $booking->payment_status }}
                        </span>
                    </div>

                    @if($booking->payment_method)
                    <div>
                        <p class="text-sm text-gray-500">Способ оплаты</p>
                        <p class="font-medium">{{ $booking->payment_method }}</p>
                    </div>
                    @endif

                    @if($booking->paid_at)
                    <div>
                        <p class="text-sm text-gray-500">Оплачено</p>
                        <p class="font-medium">{{ $booking->paid_at->format('d.m.Y H:i') }}</p>
                    </div>
                    @endif

                    <div>
                        <p class="text-sm text-gray-500">Место</p>
                        <p class="font-medium">{{ $booking->place->name }}</p>
                        <p class="text-sm text-gray-600">{{ $booking->place->address }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Создано</p>
                        <p class="font-medium">{{ $booking->created_at->format('d.m.Y H:i') }}</p>
                    </div>

                    @if($booking->expires_at)
                    <div>
                        <p class="text-sm text-gray-500">Истекает</p>
                        <p class="font-medium text-orange-600">{{ $booking->expires_at->format('d.m.Y H:i') }}</p>
                    </div>
                    @endif

                    @if($booking->comment)
                    <div>
                        <p class="text-sm text-gray-500">Комментарий</p>
                        <p class="text-sm">{{ $booking->comment }}</p>
                    </div>
                    @endif
                </div>

                <div class="mt-6 space-y-2">
                    @if($booking->canPay())
                    <a href="{{ route('admin.bookings.pay', $booking) }}" 
                       class="block w-full text-center bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
                        💳 Оплатить
                    </a>
                    @endif
                    
                    <button wire:click="deleteBooking" 
                            wire:confirm="Вы уверены, что хотите удалить это бронирование?"
                            class="block w-full text-center bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">
                        Удалить
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>