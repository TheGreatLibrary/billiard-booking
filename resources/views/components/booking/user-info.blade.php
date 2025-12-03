@props([])

<div {{ $attributes->merge(['class' => 'p-6 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-950/40 dark:to-emerald-950/30 rounded-xl border border-green-200 dark:border-green-800']) }}>
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 bg-green-100 dark:bg-green-900/40 rounded-full flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
        </div>
        <div>
            <p class="text-lg font-bold text-gray-900 dark:text-white">Бронирование на имя: {{ auth()->user()->name ?? 'Пользователь' }}</p>
            <p class="text-gray-600 dark:text-gray-300">{{ auth()->user()->email ?? '' }}</p>
            
            @if(auth()->user()->phone)
                <p class="text-gray-600 dark:text-gray-300 mt-1">
                    📞 {{ auth()->user()->phone }}
                </p>
            @endif
        </div>
    </div>
</div>