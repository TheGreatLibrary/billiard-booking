<div>
    <!-- Сообщение о статусе -->
    @if(session('status'))
        <div class="mb-6 p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="text-sm text-green-800 dark:text-green-400 font-medium">
                        {{ session('status') }}
                    </p>
                    <p class="text-xs text-green-600 dark:text-green-500 mt-1">
                        Если письмо не пришло, проверьте папку "Спам"
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Инструкция -->
    <div class="mb-8 text-center">
        <div class="w-16 h-16 bg-gradient-to-br from-amber-100 to-amber-200 dark:from-amber-900/30 dark:to-amber-800/30 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </div>
        
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">
            Восстановление пароля
        </h2>
        <p class="text-gray-600 dark:text-gray-400">
            Укажите email, который вы использовали при регистрации. 
            Мы отправим вам ссылку для сброса пароля.
        </p>
    </div>

    <form wire:submit="sendPasswordResetLink" class="space-y-6">
        <!-- Email -->
        <x-auth.input 
            wireModel="email"
            name="email"
            label="Ваш Email"
            type="email"
            placeholder="example@mail.ru"
            required
            autofocus
            autocomplete="email"
        />

        <!-- Кнопка отправки -->
        <x-auth.button 
            type="submit"
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
                    Отправить ссылку
                </span>
            </span>
            <span wire:loading>
                <svg class="animate-spin h-5 w-5 mx-auto text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </span>
        </x-auth.button>

        <!-- Сообщение об ошибке -->
        @error('email')
            <div class="p-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                <div class="flex items-center gap-2 text-red-800 dark:text-red-400">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm">{{ $message }}</span>
                </div>
            </div>
        @enderror
    </form>

    <!-- Навигация -->
    <div class="mt-8 space-y-4">
        <!-- Вернуться к входу -->
        <div class="pt-6 border-t border-gray-100 dark:border-gray-700">
            <a 
                href="{{ route('login') }}" 
                class="flex items-center justify-center gap-2 text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 transition-colors group"
                wire:navigate
            >
                <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span class="font-medium">Вернуться ко входу</span>
            </a>
        </div>

        <!-- Регистрация -->
        <div class="text-center">
            <p class="text-gray-600 dark:text-gray-400 text-sm">
                Нет аккаунта?
                <a 
                    href="{{ route('register') }}" 
                    class="font-semibold text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 ml-1"
                    wire:navigate
                >
                    Зарегистрироваться
                </a>
            </p>
        </div>
    </div>

    <!-- Дополнительная информация -->
    <div class="mt-8 p-4 bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800/30 rounded-lg">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <div>
                <p class="text-sm text-amber-800 dark:text-amber-300 font-medium mb-1">
                    Важная информация
                </p>
                <ul class="text-xs text-amber-700 dark:text-amber-400 space-y-1">
                    <li class="flex items-start gap-2">
                        <span class="mt-1">•</span>
                        <span>Ссылка действительна в течение 60 минут</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="mt-1">•</span>
                        <span>Проверьте папку "Спам", если не нашли письмо</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="mt-1">•</span>
                        <span>После сброса пароля вы сможете войти в систему</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>