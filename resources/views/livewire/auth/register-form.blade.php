<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-50 dark:bg-gray-900">
    <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white dark:bg-gray-800 shadow-xl rounded-2xl">
        <!-- Логотип -->
        <div class="flex justify-center mb-8">
            <a href="{{ route('home') }}" class="flex items-center space-x-3 group">
                <div class="w-12 h-12 bg-gradient-to-br from-amber-600 to-amber-800 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <div class="w-8 h-8 bg-black rounded-full flex items-center justify-center relative shadow-lg">
                        <div class="absolute inset-0 rounded-full border border-white/20"></div>
                        <span class="text-white text-xs font-bold z-10">8</span>
                    </div>
                </div>
                <span class="text-2xl font-bold text-gray-800 dark:text-amber-100">
                    Бильярд Клуб
                </span>
            </a>
        </div>

        <h2 class="text-3xl font-bold text-center text-gray-800 dark:text-white mb-2">
            Регистрация
        </h2>
        <p class="text-gray-600 dark:text-gray-400 text-center mb-8">
            Создайте аккаунт для доступа к клубу
        </p>

        <form wire:submit="register" class="space-y-1">
            <!-- Имя -->
            <x-auth.input 
                wireModel="name"
                name="name"
                label="Имя"
                placeholder="Введите ваше имя"
                required
            />

            <!-- Email -->
            <x-auth.input 
                wireModel="email"
                name="email"
                label="Email"
                type="email"
                placeholder="example@mail.ru"
                required
            />

            <!-- Телефон -->
            <x-auth.input 
                wireModel="phone"
                name="phone"
                label="Телефон"
                placeholder="8XXXXXXXXXX"
                required
            />

            <!-- Пароль -->
            <x-auth.input 
                wireModel="password"
                name="password"
                label="Пароль"
                type="password"
                placeholder="Минимум 8 символов"
                required
            />

            <!-- Подтверждение пароля -->
            <x-auth.input 
                wireModel="password_confirmation"
                name="password_confirmation"
                label="Подтвердите пароль"
                type="password"
                placeholder="Повторите пароль"
                required
            />

            <!-- Соглашение -->
            <div class="mb-6">
                <label class="flex items-start space-x-3">
                    <input 
                        type="checkbox" 
                        required
                        class="mt-1 rounded border-gray-300 text-amber-600 
                               focus:ring-amber-500 dark:border-gray-600 
                               dark:bg-gray-700 dark:checked:bg-amber-600"
                    >
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        Я соглашаюсь с 
                        <a href="#" class="text-amber-600 dark:text-amber-400 hover:underline">
                            условиями использования
                        </a> 
                        и 
                        <a href="#" class="text-amber-600 dark:text-amber-400 hover:underline">
                            политикой конфиденциальности
                        </a>
                    </span>
                </label>
            </div>

            <!-- Кнопка регистрации -->
            <x-auth.button 
                type="submit"
                variant="primary"
                class="w-full py-3 text-lg font-semibold"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-50 cursor-not-allowed"
            >
                <span wire:loading.remove>Зарегистрироваться</span>
                <span wire:loading>
                    <svg class="animate-spin h-5 w-5 mx-auto text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
            </x-auth.button>
        </form>

        <!-- Ссылка на вход -->
        <div class="mt-8 text-center">
            <p class="text-gray-600 dark:text-gray-400">
                Уже есть аккаунт?
                <a 
                    href="{{ route('login') }}" 
                    class="font-semibold text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 transition-colors"
                    wire:navigate
                >
                    Войти
                </a>
            </p>
        </div>
    </div>

    <!-- Дополнительная информация -->
    <div class="mt-8 text-center text-gray-500 dark:text-gray-400 text-sm">
        <p>Регистрируясь, вы получаете доступ ко всем возможностям клуба</p>
        <p class="mt-1">Бронирование столов, участие в турнирах, система лояльности</p>
    </div>
</div>