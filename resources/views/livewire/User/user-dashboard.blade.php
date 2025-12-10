<div class="max-w-7xl mx-auto p-6">
    <!-- Header -->
    <div class="mb-8"> 
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-3">
            Добро пожаловать, {{ auth()->user()->name }}!
        </h1>
        <p class="text-xl text-gray-600 dark:text-gray-400">
            Здесь вы можете управлять своими бронированиями
        </p>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <x-dashboard.flash-message type="success" :message="session('success')" />
    @endif
    @if(session('error'))
        <x-dashboard.flash-message type="error" :message="session('error')" />
    @endif

    @php
        $calendarIcon = '<svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>';
        
        $checkIcon = '<svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
        
        $shieldIcon = '<svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>';
        
        $moneyIcon = '<svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path></svg>';
        
        $plusIcon = '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>';
        
        $userIcon = '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>';
        
        $adminIcon = '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>';
        
        $supportIcon = '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
        
        $refreshIcon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>';
    @endphp

    <!-- Статистика -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-dashboard.stat-card
            title="Всего бронирований"
            :value="$stats['total']"
            description="За все время"
            color="blue"
            :icon="$calendarIcon"
        />

        <x-dashboard.stat-card
            title="Активных"
            :value="$stats['active']"
            description="Текущие брони"
            color="green"
            :icon="$checkIcon"
        />

        <x-dashboard.stat-card
            title="Завершено"
            :value="$stats['completed']"
            description="Успешные игры"
            color="blue"
            :icon="$shieldIcon"
        />

        <x-dashboard.stat-card
            title="Потрачено"
            :value="number_format($stats['total_spent'], 0, '', ' ') . ' ₽'"
            description="Общие расходы"
            color="purple"
            :icon="$moneyIcon"
        />
    </div>

    <!-- Быстрые действия -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <x-dashboard.action-card
            title="Новое бронирование"
            description="Забронировать стол на нужное время"
            :href="route('booking.create.auth')"
            color="blue"
            :icon="$plusIcon"
        />

        <x-dashboard.action-card
            title="Мой профиль"
            description="Управление личными данными"
            :href="route('profile')"
            color="green"
            :icon="$userIcon"
        />

        @if(auth()->user()->hasRole('admin'))
        <x-dashboard.action-card
            title="Админ-панель"
            description="Управление системой"
            :href="route('admin.dashboard')"
            color="purple"
            :icon="$adminIcon"
        />
        @else
        <x-dashboard.action-card
            title="Поддержка"
            description="Нужна помощь с бронированием?"
            color="gray"
            :icon="$supportIcon"
        />
        @endif
    </div>

    <!-- Мои бронирования -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg dark:shadow-gray-900/30 p-6 border border-gray-200 dark:border-gray-700">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Мои бронирования</h2>
                <p class="text-gray-600 dark:text-gray-400">История и текущие бронирования</p>
            </div>

            <x-dashboard.button 
                wire:click="$refresh"
                variant="secondary"
                class="flex items-center gap-2"
            >
                {!! $refreshIcon !!}
                <span>Обновить</span>
            </x-dashboard.button>
        </div>

        @if($recentBookings->count() > 0)
            <div class="space-y-6">
                @foreach($recentBookings as $booking)
                    <x-dashboard.booking-card :booking="$booking" />
                @endforeach
            </div>
        @else

            <x-dashboard.empty-state
                title="У вас пока нет бронирований"
                description="Создайте первое бронирование, чтобы начать играть!"
                :href="route('booking.create')"
                buttonText="Создать бронирование"
            />
        @endif
    </div>
</div>