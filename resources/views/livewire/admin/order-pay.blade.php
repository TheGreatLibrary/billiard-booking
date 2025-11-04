<div>
   <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold">Оплата заказа #{{ $order->id }}</h1>
            <p class="text-gray-600">Выберите способ оплаты</p>
        </div>
        <a href="{{ route('admin.orders.show', $order) }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
            ← Назад к заказу
        </a>
    </div>

    <div class="max-w-4xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Информация о заказе -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-lg font-semibold mb-4">📋 Детали заказа</h2>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Клиент:</span>
                            <span class="font-medium">{{ $order->user->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Email:</span>
                            <span>{{ $order->user->email }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Бронирование:</span>
                            <a href="{{ route('admin.bookings.show', $order->booking_id) }}" 
                               class="text-blue-600 hover:underline">
                                #{{ $order->booking_id }}
                            </a>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Время:</span>
                            <span>{{ $order->booking->starts_at }} - {{ $order->booking->ends_at }}</span>
                        </div>
                    </div>
                </div>

                <!-- Позиции заказа -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold mb-4">📦 Позиции</h2>
                    
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm">Наименование</th>
                                <th class="px-4 py-2 text-left text-sm">Кол-во</th>
                                <th class="px-4 py-2 text-right text-sm">Сумма</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($order->items as $item)
                            <tr>
                                <td class="px-4 py-3">
                                    @if($item->type === 'table_time')
                                        <span class="font-medium">Аренда стола</span>
                                    @else
                                        <span class="font-medium">{{ $item->productModel->name }}</span>
                                        <div class="text-xs text-gray-500">{{ number_format($item->price_each, 2) }} ₽ × {{ $item->qty }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">{{ $item->qty }}</td>
                                <td class="px-4 py-3 text-right font-medium">{{ number_format($item->amount, 2) }} ₽</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 font-bold">
                            <tr>
                                <td colspan="2" class="px-4 py-3 text-right">ИТОГО:</td>
                                <td class="px-4 py-3 text-right text-xl text-green-600">{{ number_format($order->total_amount, 2) }} ₽</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
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
                                {{ $paymentMethod === 'cash' ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                                <input type="radio" wire:model.live="paymentMethod" value="cash" class="mr-3">
                                <div class="flex-1">
                                    <div class="font-medium">💵 Наличные</div>
                                    <div class="text-xs text-gray-500">Оплата на месте</div>
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
                                <span class="text-2xl font-bold text-green-600">{{ number_format($order->total_amount, 2) }} ₽</span>
                            </div>
                        </div>

                        <!-- Кнопки -->
                        <div class="space-y-2">
                            <button type="submit" 
                                    wire:loading.attr="disabled"
                                    class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3 rounded-lg transition">
                                <span wire:loading.remove>✓ Оплатить {{ number_format($order->total_amount, 2) }} ₽</span>
                                <span wire:loading>⏳ Обработка...</span>
                            </button>

                            <a href="{{ route('admin.orders.show', $order) }}" 
                               class="block w-full bg-gray-200 hover:bg-gray-300 text-gray-700 text-center py-3 rounded-lg transition">
                                Отмена
                            </a>
                        </div>
                    </form>

                    <!-- Подсказка -->
                    <div class="mt-4 p-3 bg-blue-50 rounded text-xs text-blue-800">
                        <strong>💡 Совет:</strong> После оплаты бронирование будет подтверждено и его нельзя будет редактировать.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
