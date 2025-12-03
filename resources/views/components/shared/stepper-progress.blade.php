@props([
    'current' => 1,
    'steps' => [
        1 => ['label' => 'Место', 'icon' => 'location'],
        2 => ['label' => 'Стол', 'icon' => 'table'],
        3 => ['label' => 'Время', 'icon' => 'clock'],
        4 => ['label' => 'Оборудование', 'icon' => 'equipment'],
        5 => ['label' => 'Данные', 'icon' => 'user'],
        6 => ['label' => 'Оплата', 'icon' => 'payment'],
        7 => ['label' => 'Готово', 'icon' => 'check'],
    ]
])

@php
    $icons = [
        'location' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z',
        'table' => 'M4 6h16M4 10h16M4 14h16M4 18h16',
        'clock' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        'equipment' => 'M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z',
        'user' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
        'payment' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
        'check' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'mb-8 overflow-x-auto']) }}>
    <div class="flex min-w-max space-x-4 sm:space-x-2">
        @foreach($steps as $num => $step)
            @php
                $isCompleted = $num < $current;
                $isCurrent = $num === $current;
                $isUpcoming = $num > $current;
                $isLast = $loop->last;
            @endphp

            <div class="flex items-center {{ !$loop->last ? 'flex-1' : '' }}">
                <button 
                    @if(!$isUpcoming && $num !== 7) wire:click="goToStep({{ $num }})" @endif
                    type="button"
                    class="relative group flex items-center gap-3 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 dark:focus:ring-offset-gray-900"
                    @if($isUpcoming || $num === 7) disabled @endif
                >
                    <!-- Шаг номер/иконка -->
                    <div class="relative">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold transition-all duration-300
                            {{ $isCompleted 
                                ? 'bg-gradient-to-r from-green-500 to-emerald-500 dark:from-green-600 dark:to-emerald-600 text-white shadow-lg dark:shadow-green-800/50' 
                                : ($isCurrent 
                                    ? 'bg-gradient-to-r from-amber-600 to-amber-800 text-white shadow-xl ring-4 ring-amber-200 dark:ring-amber-900/50' 
                                    : 'bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400 shadow dark:shadow-gray-800') }}
                            {{ $isUpcoming ? 'opacity-60' : 'hover:scale-110' }}">
                            
                            @if($num === 7 && $isCompleted)
                                <!-- Иконка галочки для завершенного последнего шага -->
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            @elseif($step['icon'] && $isCompleted)
                                <!-- Иконка для завершенных шагов -->
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icons[$step['icon']] ?? '' }}"></path>
                                </svg>
                            @else
                                {{ $num }}
                            @endif
                        </div>
                        
                        <!-- Анимированный индикатор текущего шага -->
                        @if($isCurrent)
                            <span class="absolute -top-1 -right-1 flex h-6 w-6">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75 dark:bg-amber-500"></span>
                                <span class="relative inline-flex rounded-full h-6 w-6 bg-gradient-to-r from-amber-500 to-amber-700"></span>
                            </span>
                        @endif
                    </div>

                    <!-- Название шага -->
                    <div class="text-left">
                        <span class="block text-sm font-medium transition-colors 
                            {{ $isCompleted || $isCurrent 
                                ? 'text-gray-900 dark:text-white' 
                                : 'text-gray-500 dark:text-gray-400' }}">
                            {{ $step['label'] }}
                        </span>
                        @if($isCompleted)
                            <span class="block text-xs text-green-600 dark:text-green-400 font-medium">
                                ✓ Выполнено
                            </span>
                        @elseif($isCurrent)
                            <span class="block text-xs text-amber-600 dark:text-amber-400 font-medium animate-pulse">
                                ● Текущий шаг
                            </span>
                        @endif
                    </div>
                </button>

                <!-- Линия соединения (кроме последнего шага) -->
                @if(!$loop->last)
                    <div class="flex-1 mx-4 sm:mx-6 min-w-[40px]">
                        <div class="h-1 w-full rounded-full transition-colors 
                            {{ $num < $current 
                                ? 'bg-gradient-to-r from-green-500 to-emerald-500 dark:from-green-600 dark:to-emerald-600' 
                                : 'bg-gray-200 dark:bg-gray-700' }}"></div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>