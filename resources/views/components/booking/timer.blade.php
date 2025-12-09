@props([
    'expiresAt' => null,
])

@php
    try {
        $expires = $expiresAt ? \Carbon\Carbon::parse($expiresAt) : now()->addMinutes(30);
    } catch (Exception $e) {
        $expires = now()->addMinutes(30);
    }
    
    // Округляем до целых минут в большую сторону
    $minutesLeft = max(0, ceil(now()->diffInSeconds($expires, false) / 60));
    $isCritical = $minutesLeft < 10;
    
    // Функция для правильного склонения минут
    function formatMinutes($minutes) {
        $minutes = (int)$minutes; // Приводим к целому числу
        
        $lastDigit = $minutes % 10;
        $lastTwoDigits = $minutes % 100;
        
        if ($lastTwoDigits >= 11 && $lastTwoDigits <= 19) {
            return $minutes . ' минут';
        }
        
        switch ($lastDigit) {
            case 1:
                return $minutes . ' минуту';
            case 2:
            case 3:
            case 4:
                return $minutes . ' минуты';
            default:
                return $minutes . ' минут';
        }
    }
    
    $formattedTime = formatMinutes($minutesLeft);
@endphp

<div class="p-6 {{ $isCritical ? 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800' : 'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200 dark:border-yellow-800' }} rounded-xl border-2 mb-6">
    <div class="flex items-center gap-4">
        <div class="flex-shrink-0">
            <div class="w-10 h-10 {{ $isCritical ? 'bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400' : 'bg-yellow-100 dark:bg-yellow-900/40 text-yellow-600 dark:text-yellow-400' }} rounded-full flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
        <div class="flex-1">
            <p class="font-semibold {{ $isCritical ? 'text-red-800 dark:text-red-200' : 'text-yellow-800 dark:text-yellow-200' }}">
                @if($isCritical)
                    ⚠️ Срочно! Оплатите в течение {{ $formattedTime }}
                @else
                    ⏱ Оплатите в течение {{ $formattedTime }}
                @endif
            </p>
            <p class="text-sm {{ $isCritical ? 'text-red-600 dark:text-red-300' : 'text-yellow-600 dark:text-yellow-300' }} mt-1">
                Истекает: {{ $expires->translatedFormat('d.m.Y в H:i') }}
            </p>
        </div>
    </div>
</div>