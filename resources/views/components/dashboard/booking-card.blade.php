@props(['booking'])

<div class="border border-gray-200 dark:border-gray-700 rounded-xl p-6 hover:shadow-md transition-all duration-200">
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start gap-6">
        <!-- Левая часть -->
        <div class="flex-1">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/40 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-xl text-gray-900 dark:text-white">
                            {{ $booking->place->name ?? 'Место не указано' }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $booking->place->address ?? 'Адрес не указан' }}
                        </p>
                    </div>
                </div>
                <div class="lg:hidden">
                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full
                        @if(($booking->status ?? '') === 'confirmed') bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-200
                        @elseif(($booking->status ?? '') === 'pending') bg-yellow-100 dark:bg-yellow-900/40 text-yellow-800 dark:text-yellow-200
                        @elseif(($booking->status ?? '') === 'canceled') bg-gray-100 dark:bg-gray-700 text-red-600 dark:text-red-400
                        @elseif(($booking->status ?? '') === 'finished') bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-200
                        @else bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200
                        @endif">
                        {{ $booking->status ?? 'unknown' }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Стол -->
                <div class="flex items-center text-sm">
                    <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Стол:</span>
                        <span class="font-medium text-gray-900 dark:text-white ml-2">
                            {{ $booking->resource->code ?? 'N/A' }} - {{ $booking->resource->model->name ?? 'N/A' }}
                        </span>
                    </div>
                </div>

                <!-- Дата создания -->
                <div class="flex items-center text-sm">
                    <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Создано:</span>
                        <span class="font-medium text-gray-900 dark:text-white ml-2">
                            {{ $booking->created_at?->format('d.m.Y') ?? 'N/A' }}
                        </span>
                    </div>
                </div>

                <!-- Стоимость  -->
                <div class="flex items-center text-sm">
                    <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Стоимость:</span>
                        <span class="font-bold text-lg text-gray-900 dark:text-white ml-2">
                            {{ number_format(($booking->total_amount ?? 0) / 100, 0, '', ' ') }} ₽
                        </span>
                    </div>
                </div>

                <!-- Временные слоты -->
                <div class="flex items-start text-sm md:col-span-2">
                    <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center mr-3 mt-1">
                        <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Время:</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">
                                {{ $booking->slots?->count() ?? 0 }} час(ов)
                            </span>
                        </div>
                        <div class="flex flex-wrap gap-2 mt-2">
                            @forelse($booking->slots?->take(5) ?? [] as $slot)
                                <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-200 rounded-lg text-sm font-medium">
                                    {{ $slot->slot_datetime ? \Carbon\Carbon::parse($slot->slot_datetime)->format('d.m H:i') : 'N/A' }}
                                </span>
                            @empty
                                <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-lg text-sm">
                                    Нет слотов
                                </span>
                            @endforelse
                            @if(($booking->slots?->count() ?? 0) > 5)
                                <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-lg text-sm">
                                    +{{ $booking->slots->count() - 5 }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Оборудование (если есть) -->
                @if(($booking->equipment?->count() ?? 0) > 0)
                    <div class="flex items-start text-sm md:col-span-2 mt-2">
                        <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center mr-3 mt-1">
                            <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <span class="text-gray-600 dark:text-gray-400">Оборудование:</span>
                            <div class="flex flex-wrap gap-2 mt-2">
                                @foreach($booking->equipment as $item)
                                    <span class="px-3 py-1 bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-200 rounded-lg text-sm font-medium">
                                        {{ $item->productModel->name ?? 'N/A' }} ×{{ $item->qty ?? 1 }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Правая часть - только статусы и кнопки -->
        <div class="flex flex-col items-start lg:items-end space-y-4 lg:min-w-[180px]">
            <!-- Статус брони -->
            <div class="hidden lg:block w-full text-right">
                <span class="inline-block px-4 py-2 text-sm font-semibold rounded-full
                    @if(($booking->status ?? '') === 'confirmed') bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-200
                    @elseif(($booking->status ?? '') === 'pending') bg-yellow-100 dark:bg-yellow-900/40 text-yellow-800 dark:text-yellow-200
                    @elseif(($booking->status ?? '') === 'canceled') bg-gray-100 dark:bg-gray-700 text-red-600 dark:text-red-400
                    @elseif(($booking->status ?? '') === 'finished') bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-200
                    @else bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200
                    @endif">
                    {{ $booking->status ?? 'unknown' }}
                </span>
            </div>

            <!-- Статус оплаты -->
            <div class="w-full text-right">
                <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full
                    @if(($booking->payment_status ?? '') === 'paid') bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-200
                    @elseif(($booking->payment_status ?? '') === 'pending') bg-yellow-100 dark:bg-yellow-900/40 text-yellow-800 dark:text-yellow-200
                    @else bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200
                    @endif">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    {{ $booking->payment_status ?? 'unknown' }}
                </span>
            </div>

            <!-- Действия -->
            <div class="w-full text-right">
                @if(($booking->payment_status ?? '') === 'pending' && in_array($booking->status ?? '', ['pending', 'confirmed']))
                    <button wire:click="cancelBooking({{ $booking->id }})"
                            wire:confirm="Вы уверены, что хотите отменить это бронирование?"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-red-500 hover:bg-red-600 dark:bg-red-600 dark:hover:bg-red-700 text-white text-sm rounded-lg transition-colors font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Отменить
                    </button>
                @endif
            </div>

            <!-- Истекает через -->
            @if($booking->expires_at && ($booking->payment_status ?? '') === 'pending')
                <div class="w-full text-right text-sm text-orange-600 dark:text-orange-400 font-medium flex items-center gap-2 justify-end">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ $booking->expires_at->diffForHumans() }}
                </div>
            @endif
        </div>
    </div>
</div>