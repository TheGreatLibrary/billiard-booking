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
                        <p class="font-medium">{{ $booking->user->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Телефон</p>
                        <p class="font-medium">{{ $booking->user->phone }}</p>
                    </div>
                    @if($booking->user->email)
                    <div>
                        <p class="text-sm text-gray-500">Email</p>
                        <p class="font-medium">{{ $booking->user->email }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Забронированные столы -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold mb-4">Забронированные столы</h2>
                
                @foreach($booking->bookingResources as $br)
                <div class="border-b pb-4 mb-4 last:border-b-0 last:mb-0 last:pb-0">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-medium text-lg">🎱 {{ $br->resource->code ?? 'Стол' }}</p>
                            <p class="text-sm text-gray-500">{{ $br->resource->model->name }}</p>
                            <p class="text-sm text-gray-600 mt-1">
                                Зона: {{ $br->resource->zone->name }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-green-600">{{ number_format($br->amount, 0, ',', ' ') }} ₽</p>
                            <p class="text-sm text-gray-500">{{ $br->minutes }} минут</p>
                        </div>
                    </div>
                    
                    <div class="mt-3 grid grid-cols-2 gap-2 text-sm">
                        <div>
                            <span class="text-gray-500">Начало:</span>
                            <span class="font-medium">{{ \Carbon\Carbon::parse($br->starts_at)->format('d.m.Y H:i') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Окончание:</span>
                            <span class="font-medium">{{ \Carbon\Carbon::parse($br->ends_at)->format('d.m.Y H:i') }}</span>
                        </div>
                    </div>

                    <!-- Детали ценообразования -->
                    <details class="mt-3">
                        <summary class="cursor-pointer text-sm text-blue-600 hover:text-blue-800">
                            Детали расчёта цены
                        </summary>
                        <div class="mt-2 p-3 bg-gray-50 rounded text-sm space-y-1">
                            <p>Базовая цена/час: {{ number_format($br->hour_price_snapshot, 0, ',', ' ') }} ₽</p>
                            <p>Коэффициент зоны: {{ $br->zone_coef_snapshot }}</p>
                            <p>Правило: {{ $br->rule_kind }} = {{ $br->rule_value }}</p>
                        </div>
                    </details>
                </div>
                @endforeach
            </div>

            <!-- Заказ (если есть) -->
            @if($booking->order)
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold">Заказ</h2>
                    <a href="{{ route('admin.orders.show', $booking->order) }}" 
                       class="text-blue-600 hover:text-blue-800 text-sm">
                        Посмотреть полностью →
                    </a>
                </div>
                
                <div class="space-y-2">
                    @foreach($booking->order->items as $item)
                    <div class="flex justify-between items-center py-2 border-b last:border-b-0">
                        <div>
                            @if($item->type === 'table_time')
                                <p class="font-medium">Аренда стола</p>
                            @else
                                <p class="font-medium">{{ $item->productModel->name }}</p>
                                <p class="text-sm text-gray-500">x{{ $item->qty }}</p>
                            @endif
                        </div>
                        <p class="font-semibold">{{ number_format($item->amount, 0, ',', ' ') }} ₽</p>
                    </div>
                    @endforeach
                    
                    <div class="flex justify-between items-center pt-3 border-t-2 border-gray-300">
                        <p class="font-bold text-lg">ИТОГО:</p>
                        <p class="font-bold text-xl text-green-600">
                            {{ number_format($booking->order->total_amount, 0, ',', ' ') }} ₽
                        </p>
                    </div>
                </div>
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
                        <p class="text-sm text-gray-500 mb-1">Текущий статус</p>
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
                        <p class="text-sm text-gray-500">Место</p>
                        <p class="font-medium">{{ $booking->place->name }}</p>
                        <p class="text-sm text-gray-600">{{ $booking->place->address }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Создано</p>
                        <p class="font-medium">{{ $booking->created_at->format('d.m.Y H:i') }}</p>
                    </div>

                    @if($booking->comment)
                    <div>
                        <p class="text-sm text-gray-500">Комментарий</p>
                        <p class="text-sm">{{ $booking->comment }}</p>
                    </div>
                    @endif
                </div>

                <div class="mt-6 space-y-2">
                    <a href="{{ route('admin.bookings.edit', $booking) }}" 
                       class="block w-full text-center bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                        Редактировать
                    </a>
                    
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
