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
    ],
    'compact' => false, // Компактный режим для мобильных
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

<div {{ $attributes->merge(['class' => 'mb-8']) }}>
    <div class="flex items-center justify-between">
        @foreach($steps as $num => $step)
            @php
                $isCompleted = $num < $current;
                $isCurrent = $num === $current;
                $isUpcoming = $num > $current;
                $isLast = $loop->last;
            @endphp

            <div class="flex items-center flex-1 {{ $isLast ? '' : 'relative' }}">
                <!-- Шаг -->
                <div class="relative z-10 flex flex-col items-center text-center w-full">
                    <!-- Круг шага -->
                    <div class="w-{{ $compact ? '8' : '10' }} h-{{ $compact ? '8' : '10' }} rounded-full flex items-center justify-center font-bold transition-all duration-300
                        {{ $isCompleted 
                            ? 'bg-gradient-to-r from-green-500 to-emerald-500 dark:from-green-600 dark:to-emerald-600 text-white' 
                            : ($isCurrent 
                                ? 'bg-gradient-to-r from-amber-600 to-amber-800 text-white shadow-lg ring-2 ring-amber-200 dark:ring-amber-900/50' 
                                : 'bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500') }}
                        {{ $isCurrent ? 'scale-110' : '' }}">
                        
                        @if($num === 7 && $isCompleted)
                            <!-- Иконка галочки для завершенного последнего шага -->
                            <svg class="w-{{ $compact ? '4' : '5' }} h-{{ $compact ? '4' : '5' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        @elseif($step['icon'] && $isCompleted && !$compact)
                            <!-- Иконка для завершенных шагов -->
                            <svg class="w-{{ $compact ? '4' : '5' }} h-{{ $compact ? '4' : '5' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icons[$step['icon']] ?? '' }}"></path>
                            </svg>
                        @else
                            <span class="text-{{ $compact ? 'xs' : 'sm' }} font-medium">
                                @if($isCompleted && !$compact && $step['icon'])
                                    <!-- Показываем иконку для завершенных -->
                                @else
                                    {{ $num }}
                                @endif
                            </span>
                        @endif
                        
                        <!-- Анимированный индикатор текущего шага -->
                        @if($isCurrent)
                            <span class="absolute -top-1 -right-1 flex h-{{ $compact ? '4' : '5' }} w-{{ $compact ? '4' : '5' }}">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75 dark:bg-amber-500"></span>
                                <span class="relative inline-flex rounded-full h-full w-full bg-gradient-to-r from-amber-500 to-amber-700"></span>
                            </span>
                        @endif
                    </div>

                    <!-- Название шага -->
                    <div class="mt-2 w-full px-1">
                        <span class="block text-{{ $compact ? 'xs' : 'sm' }} font-medium transition-colors whitespace-nowrap overflow-hidden text-ellipsis
                            {{ $isCompleted || $isCurrent 
                                ? 'text-gray-900 dark:text-white' 
                                : 'text-gray-500 dark:text-gray-400' }}">
                            {{ $step['label'] }}
                        </span>
                        
                        @if(!$compact)
                            @if($isCompleted)
                                <span class="block text-xs text-green-600 dark:text-green-400 font-medium mt-1">
                                    ✓
                                </span>
                            @elseif($isCurrent)
                                <span class="block text-xs text-amber-600 dark:text-amber-400 font-medium mt-1 animate-pulse">
                                    ●
                                </span>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- Линия соединения (кроме последнего шага) -->
                @if(!$loop->last)
                    <div class="absolute left-1/2 top-{{ $compact ? '4' : '5' }} w-full h-0.5 -translate-y-1/2 -z-10">
                        <div class="h-full w-full rounded-full transition-colors 
                            {{ $num < $current 
                                ? 'bg-gradient-to-r from-green-500 to-emerald-500 dark:from-green-600 dark:to-emerald-600' 
                                : 'bg-gray-200 dark:bg-gray-700' }}"></div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
    
    <!-- Индикатор прогресса -->
    @if(!$compact)
        <div class="mt-4">
            <div class="text-center text-sm text-gray-600 dark:text-gray-400">
                Шаг {{ $current }} из {{ count($steps) }}
            </div>
            <div class="mt-2 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                <div class="bg-gradient-to-r from-amber-500 to-amber-700 h-2 rounded-full transition-all duration-500"
                     style="width: {{ ($current / count($steps)) * 100 }}%"></div>
            </div>
        </div>
    @endif
</div>

@push('styles')
<style>
    /* Адаптивные стили */
    @media (max-width: 640px) {
        .stepper-step-label {
            font-size: 0.7rem;
            line-height: 1;
        }
        
        .stepper-step-circle {
            width: 1.75rem;
            height: 1.75rem;
            font-size: 0.75rem;
        }
    }
    
    @media (min-width: 641px) and (max-width: 768px) {
        .stepper-step-label {
            font-size: 0.8rem;
        }
        
        .stepper-step-circle {
            width: 2rem;
            height: 2rem;
            font-size: 0.875rem;
        }
    }
</style>
@endpush