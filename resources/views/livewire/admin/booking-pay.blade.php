<div>
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold">Оплата бронирования #{{ $booking->id }}</h1>
            <p class="text-gray-600">Выберите способ оплаты</p>
        </div>
        <a href="{{ route('admin.bookings.show', $booking) }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
            ← Назад
        </a>
    </div>

    <div class="max-w-4xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Информация о бронировании -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-lg font-semibold mb-4">📋 Детали бронирования</h2>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Клиент:</span>
                            <span class="font-medium">{{ $booking->getClientName() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Email:</span>
                            <span>{{ $booking->getClientEmail() ?? 'Не указан' }}</span>
                        </div>
                        @if(method_exists($booking, 'getClientPhone') && $booking->getClientPhone())
                        <div class="flex justify-between">
                            <span class="text-gray-600">Телефон:</span>
                            <span>{{ $booking->getClientPhone() }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-600">Место:</span>
                            <span>{{ $booking->place->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Столы:</span>
                            <span>
                                @php $payRes = $booking->getBookedResources(); @endphp
                                {{ $payRes->pluck('code')->implode(', ') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Временные слоты -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-lg font-semibold mb-4">🕐 Забронированное время</h2>
                    
                    @php $uniquePayTimes = $booking->slots->pluck('slot_time')->unique()->sort()->values(); @endphp
                    <div class="flex flex-wrap gap-2">
                        @foreach($uniquePayTimes as $time)
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                                {{ $booking->slots->first()?->slot_date }} {{ $time }}
                            </span>
                        @endforeach
                    </div>
                    <p class="text-sm text-gray-500 mt-3">
                        {{ $uniquePayTimes->count() }} час(ов) × {{ $payRes->count() }} столов
                    </p>
                </div>

                <!-- Оборудование -->
                @if($booking->equipment->count() > 0)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold mb-4">📦 Дополнительное оборудование</h2>
                    
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm">Наименование</th>
                                <th class="px-4 py-2 text-left text-sm">Кол-во</th>
                                <th class="px-4 py-2 text-right text-sm">Сумма</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($booking->equipment as $item)
                            <tr>
                                <td class="px-4 py-3">
                                    <span class="font-medium">{{ $item->productModel->name }}</span>
                                    <div class="text-xs text-gray-500">
                                        {{ number_format($item->price_each / 100, 2) }} ₽ × {{ $item->qty }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">{{ $item->qty }}</td>
                                <td class="px-4 py-3 text-right font-medium">
                                    {{ number_format($item->amount / 100, 0) }} ₽
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>

            <!-- Форма оплаты -->
            <div>
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-6">
                    <h2 class="text-lg font-semibold mb-4">💳 Способ оплаты</h2>
                    
                    <form wire:submit="pay">
                        <div class="space-y-3 mb-6">
                            <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition
                                {{ $paymentMethod === 'card' ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                                <input type="radio" wire:model.live="paymentMethod" value="card" class="mr-3">
                                <div class="flex-1">
                                    <div class="font-medium">💳 Банковская карта</div>
                                    <div class="text-xs text-gray-500">Visa, MasterCard, МИР</div>
                                </div>
                            </label>

                            <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition
                                {{ $paymentMethod === 'online' ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                                <input type="radio" wire:model.live="paymentMethod" value="online" class="mr-3">
                                <div class="flex-1">
                                    <div class="font-medium">🌐 Онлайн-оплата</div>
                                    <div class="text-xs text-gray-500">СБП, ЮMoney</div>
                                </div>
                            </label>
                        </div>

                        @error('paymentMethod')
                            <div class="mb-4 p-3 bg-red-50 text-red-600 rounded text-sm">
                                {{ $message }}
                            </div>
                        @enderror

                        <!-- Итоговая сумма -->
                        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">К оплате:</span>
                                <span class="text-2xl font-bold text-green-600">
                                    {{ $booking->getTotalAmountFormatted() }}
                                </span>
                            </div>
                        </div>

                        <!-- Кнопки -->
                        <div class="space-y-2">
                            <button type="submit" 
                                    wire:loading.attr="disabled"
                                    class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed">
                                <span wire:loading.remove>✓ Оплатить {{ $booking->getTotalAmountFormatted() }}</span>
                                <span wire:loading>⏳ Обработка...</span>
                            </button>

                            <a href="{{ route('admin.bookings.show', $booking) }}" 
                               class="block w-full bg-gray-200 hover:bg-gray-300 text-gray-700 text-center py-3 rounded-lg transition">
                                Отмена
                            </a>
                        </div>
                    </form>

                    <!-- Подсказка -->
                    <div class="mt-4 p-3 bg-blue-50 rounded text-xs text-blue-800">
                        <strong>💡 Совет:</strong> После оплаты бронирование будет подтверждено.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>