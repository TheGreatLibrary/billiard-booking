{{-- resources/views/livewire/auth/reset-password.blade.php --}}
<div>
    <!-- Заголовок -->
    <div class="mb-8 text-center">
        <div class="w-16 h-16 bg-gradient-to-br from-green-100 to-green-200 dark:from-green-900/30 dark:to-green-800/30 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>
        
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">
            Создание нового пароля
        </h2>
        <p class="text-gray-600 dark:text-gray-400">
            Придумайте надежный пароль для вашего аккаунта
        </p>
    </div>

    <!-- Информация о аккаунте -->
    @if($email)
    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-amber-100 to-amber-200 dark:from-amber-900/20 dark:to-amber-800/20 rounded-full flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Аккаунт для восстановления</p>
                <p class="text-gray-800 dark:text-white font-medium">{{ $email }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Требования к паролю -->
    <div class="mb-6 p-4 bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800/30 rounded-lg">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <div>
                <p class="text-sm text-amber-800 dark:text-amber-300 font-medium mb-2">
                    Требования к паролю
                </p>
                <ul class="text-xs text-amber-700 dark:text-amber-400 space-y-1">
                    <li class="flex items-center gap-2">
                        <svg wire:ignore class="w-3 h-3" :class="password.length >= 8 ? 'text-green-500' : 'text-gray-400'" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span>Минимум 8 символов</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="w-3 h-3 flex items-center justify-center">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                        </span>
                        <span>Сочетание букв и цифр</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <form wire:submit="resetPassword" class="space-y-6">
        <!-- Email (скрытое или только для чтения) -->
        <div class="hidden">
            <input type="hidden" wire:model="email">
            <input type="hidden" wire:model="token">
        </div>

        <!-- Пароль -->
        <x-auth.input 
            wireModel="password"
            name="password"
            label="Новый пароль"
            type="password"
            placeholder="Придумайте надежный пароль"
            required
            autofocus
            autocomplete="new-password"
        />

        <!-- Подтверждение пароля -->
        <x-auth.input 
            wireModel="password_confirmation"
            name="password_confirmation"
            label="Подтвердите пароль"
            type="password"
            placeholder="Повторите новый пароль"
            required
            autocomplete="new-password"
        />

        <!-- Кнопка сброса пароля -->
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Установить новый пароль
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

        @error('password')
            <div class="p-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                <div class="flex items-center gap-2 text-red-800 dark:text-red-400">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm">{{ $message }}</span>
                </div>
            </div>
        @enderror

        @if(session('status'))
            <div class="p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="text-sm text-green-800 dark:text-green-400 font-medium">
                            {{ session('status') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif
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

        <!-- Справка -->
        <div class="text-center">
            <a 
                href="{{ route('password.request') }}" 
                class="inline-flex items-center gap-1 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-300 transition-colors"
                wire:navigate
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Нужна помощь?</span>
            </a>
        </div>
    </div>

    <!-- Дополнительная информация -->
    <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5 2a2 2 0 00-2 2v14l3.5-2 3.5 2 3.5-2 3.5 2V4a2 2 0 00-2-2H5zm4.707 3.707a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L8.414 9H10a3 3 0 013 3v1a1 1 0 102 0v-1a5 5 0 00-5-5H8.414l1.293-1.293z" clip-rule="evenodd"/>
            </svg>
            <div>
                <p class="text-sm text-gray-700 dark:text-gray-300 font-medium mb-1">
                    После сброса пароля:
                </p>
                <ul class="text-xs text-gray-600 dark:text-gray-400 space-y-1">
                    <li>• Вы будете перенаправлены на страницу входа</li>
                    <li>• Используйте новый пароль для входа в систему</li>
                    <li>• Рекомендуем сохранить пароль в надежном месте</li>
                </ul>
            </div>
        </div>
    </div>
</div>