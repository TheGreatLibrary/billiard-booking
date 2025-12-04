<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Заголовок -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-3">Мой профиль</h1>
        <p class="text-xl text-gray-600 dark:text-gray-400">Управление личными данными и настройками аккаунта</p>
    </div>

    <!-- Flash Messages -->
    <x-flash-message type="success" :message="session('success')" />
    <x-flash-message type="error" :message="session('error')" />

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
        <!-- Личные данные -->
        <x-profile.card title="Личные данные" color="blue">
            <form wire:submit="updateProfile" class="space-y-6">
                <!-- Имя -->
                <x-profile.input 
                    wire:model="name"
                    label="Имя"
                    icon="user"
                    required
                    placeholder="Введите ваше имя"
                    :error="$errors->first('name')"
                />
                
                <!-- Телефон -->
                <x-profile.input 
                    wire:model="phone"
                    label="Телефон"
                    type="tel"
                    icon="phone"
                    required
                    placeholder="+7 (XXX) XXX-XX-XX"
                    :error="$errors->first('phone')"
                />
                
                <!-- Email -->
                <x-profile.input 
                    wire:model="email"
                    label="Email"
                    type="email"
                    icon="email"
                    placeholder="your@email.com"
                    :error="$errors->first('email')"
                />
                
                <!-- Кнопка сохранения -->
                <x-auth.button 
                    type="submit"
                    variant="primary"
                    class="w-full py-4 text-lg"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove>Сохранить изменения</span>
                    <span wire:loading>Сохранение...</span>
                </x-auth.button>
            </form>
        </x-profile.card>

        <!-- Изменение пароля -->
        <x-profile.card title="Изменить пароль" color="green">
            <form wire:submit="updatePassword" class="space-y-6">
                <!-- Текущий пароль -->
                <x-profile.input 
                    wire:model="current_password"
                    label="Текущий пароль"
                    type="password"
                    icon="lock"
                    required
                    placeholder="Введите текущий пароль"
                    :error="$errors->first('current_password')"
                />
                
                <!-- Новый пароль -->
                <x-profile.input 
                    wire:model="password"
                    label="Новый пароль"
                    type="password"
                    icon="lock"
                    required
                    placeholder="Введите новый пароль"
                    :error="$errors->first('password')"
                />
                
                <!-- Подтверждение пароля -->
                <x-profile.input 
                    wire:model="password_confirmation"
                    label="Подтверждение пароля"
                    type="password"
                    icon="lock"
                    required
                    placeholder="Повторите новый пароль"
                />
                
                <!-- Кнопка изменения пароля -->
                <x-auth.button 
                    type="submit"
                    variant="success"
                    class="w-full py-4 text-lg"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove>Изменить пароль</span>
                    <span wire:loading>Изменение...</span>
                </x-auth.button>
            </form>
        </x-profile.card>
    </div>

    <!-- Информация об аккаунте -->
    <x-profile.card title="Информация об аккаунте" color="purple" class="mt-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Дата регистрации -->
            <x-profile.info-item 
                label="Дата регистрации"
                :value="auth()->user()->created_at->format('d.m.Y H:i')"
                icon="calendar"
                color="blue"
            />
            
            <!-- Роль -->
            <x-profile.info-item 
                label="Роль"
                :value="auth()->user()->hasRole('admin') ? 'Администратор' : 'Пользователь'"
                icon="shield"
                color="green"
                :highlight="auth()->user()->hasRole('admin')"
            />
        </div>
    </x-profile.card>
</div>