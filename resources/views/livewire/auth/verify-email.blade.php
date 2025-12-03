<div>
    <!-- Иконка и заголовок -->
    <div class="mb-8 text-center">
        <div class="w-20 h-20 bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-900/30 dark:to-blue-800/30 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-10 h-10 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </div>
        
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">
            Подтверждение email
        </h2>
        <p class="text-gray-600 dark:text-gray-400">
            Завершите регистрацию вашего аккаунта
        </p>
    </div>

    <!-- Основное сообщение -->
    <div class="mb-8 p-6 bg-blue-50 dark:bg-blue-900/10 rounded-xl border border-blue-200 dark:border-blue-800/30">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">
                    Подтвердите ваш email
                </h3>
                <p class="text-gray-600 dark:text-gray-400">
                    Благодарим за регистрацию! Прежде чем начать, пожалуйста, подтвердите ваш email, 
                    перейдя по ссылке в письме, которое мы отправили на адрес 
                    <span class="font-semibold text-gray-800 dark:text-white">{{ Auth::user()->email }}</span>.
                </p>
                <p class="text-gray-600 dark:text-gray-400 mt-3">
                    Если вы не получили письмо, мы с радостью отправим вам новое.
                </p>
            </div>
        </div>
    </div>

    <!-- Успешная отправка -->
    @if (session('status') == 'verification-link-sent')
        <div class="mb-6 p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="text-sm text-green-800 dark:text-green-400 font-medium">
                        Новое письмо отправлено!
                    </p>
                    <p class="text-xs text-green-600 dark:text-green-500 mt-1">
                        Проверьте ваш email {{ Auth::user()->email }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Карточка аккаунта -->
    <div class="mb-8 p-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-amber-100 to-amber-200 dark:from-amber-900/20 dark:to-amber-800/20 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Зарегистрированный аккаунт</p>
                    <p class="text-gray-800 dark:text-white font-medium">{{ Auth::user()->name }}</p>
                </div>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                    </svg>
                    Ожидает подтверждения
                </span>
            </div>
        </div>
        
        <div class="border-t border-gray-100 dark:border-gray-700 pt-4">
            <p class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                </svg>
                {{ Auth::user()->email }}
            </p>
        </div>
    </div>

    <!-- Кнопки действий -->
    <div class="space-y-4">
        <!-- Кнопка отправки письма -->
        <x-auth.button 
            wire:click="sendVerification"
            variant="primary"
            class="w-full py-3 text-lg font-semibold"
            wire:loading.attr="disabled"
            wire:loading.class="opacity-50 cursor-not-allowed"
        >
            <span wire:loading.remove>
                <span class="flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Отправить письмо повторно
                </span>
            </span>
            <span wire:loading>
                <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </span>
        </x-auth.button>

        <!-- Кнопка выхода -->
        <button wire:click="logout" 
                type="button"
                class="w-full flex items-center justify-center gap-2 px-6 py-3 rounded-lg border border-gray-300 dark:border-gray-600 
                       text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 
                       transition-colors font-medium"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            Выйти и войти под другим аккаунтом
        </button>
    </div>

    <!-- Дополнительная информация -->
    <div class="mt-8 space-y-6">
        <!-- Советы -->
        <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="text-sm text-gray-700 dark:text-gray-300 font-medium mb-2">
                        Что делать дальше?
                    </p>
                    <ul class="text-xs text-gray-600 dark:text-gray-400 space-y-1">
                        <li class="flex items-start gap-2">
                            <span class="mt-1">•</span>
                            <span>Проверьте папку "Спам" или "Рассылки" в вашем почтовом ящике</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-1">•</span>
                            <span>Нажмите на ссылку в письме для подтверждения email</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-1">•</span>
                            <span>После подтверждения вы автоматически попадете в личный кабинет</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Навигация -->
        <div class="text-center">
            <a href="{{ route('home') }}" 
               class="inline-flex items-center gap-2 text-sm text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 transition-colors"
               wire:navigate
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Вернуться на главную страницу
            </a>
        </div>
    </div>
</div>