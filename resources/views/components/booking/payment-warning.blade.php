@props([])

<div {{ $attributes->merge(['class' => 'mb-8 p-6 bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-yellow-950/40 dark:to-orange-950/30 rounded-xl border-2 border-yellow-200 dark:border-yellow-800']) }}>
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/40 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        </div>
        <div class="text-left">
            <p class="font-semibold text-yellow-800 dark:text-yellow-200 mb-1">
                ⚠️ Не забудьте оплатить в течение <strong>30 минут</strong>
            </p>
            <p class="text-sm text-yellow-700 dark:text-yellow-300">
                Иначе бронирование будет автоматически отменено
            </p>
        </div>
    </div>
</div>