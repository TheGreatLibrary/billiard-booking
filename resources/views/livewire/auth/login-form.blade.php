
<div>
    <!-- Сообщения о статусе -->
    @if(session('status'))
        <div class="mb-6 p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800">
            <div class="flex items-center gap-2 text-green-800 dark:text-green-400">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm">{{ session('status') }}</span>
            </div>
        </div>
    @endif

    <form wire:submit="login" class="space-y-6">
        <!-- Email -->
        <x-auth.input 
            wireModel="form.email"
            name="email"
            label="Email"
            type="email"
            placeholder="example@mail.ru"
            required
            autofocus
            autocomplete="username"
        />

        <!-- Пароль -->
        <x-auth.input 
            wireModel="form.password"
            name="password"
            label="Пароль"
            type="password"
            placeholder="Введите ваш пароль"
            required
            autocomplete="current-password"
        />

        <!-- Запомнить меня и забыли пароль -->
        <div class="flex items-center justify-between">
            <label class="flex items-center space-x-2 cursor-pointer">
                <input 
                    type="checkbox"
                    wire:model="form.remember"
                    name="remember"
                    class="w-4 h-4 rounded border-gray-300 text-amber-600 
                           focus:ring-amber-500 dark:border-gray-600 
                           dark:bg-gray-700 dark:checked:bg-amber-600"
                >
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    Запомнить меня
                </span>
            </label>

            <!-- Забыли пароль -->
            @if (Route::has('password.request'))
                <a 
                    href="{{ route('password.request') }}" 
                    class="text-sm text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 hover:underline transition-colors"
                    wire:navigate
                >
                    Забыли пароль?
                </a>
            @endif
        </div>

        <!-- Кнопка входа -->
        <x-auth.button 
            type="submit"
            variant="primary"
            class="w-full py-3 text-lg font-semibold mt-8"
            wire:loading.attr="disabled"
            wire:loading.class="opacity-50 cursor-not-allowed"
        >
            <span wire:loading.remove>Войти</span>
            <span wire:loading>
                <svg class="animate-spin h-5 w-5 mx-auto text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </span>
        </x-auth.button>

        <!-- Сообщение об ошибках аутентификации -->
        @error('form.email')
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

    <!-- Ссылка на регистрацию -->
    <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-center gap-4">
            <a 
                href="{{ route('register') }}" 
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg 
                       bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 
                       hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-sm"
                wire:navigate
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                Зарегистрироваться
            </a>
            
            <a 
                href="{{ route('home') }}" 
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg 
                       border border-gray-300 dark:border-gray-600 
                       text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-sm"
                wire:navigate
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                На главную
            </a>
        </div>
    </div>
</div>