@props([])

<div {{ $attributes->merge(['class' => 'space-y-6']) }}>
    <div class="p-6 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-950/40 dark:to-indigo-950/30 rounded-xl border border-blue-200 dark:border-blue-800">
        <h3 class="font-semibold text-lg text-gray-900 dark:text-white mb-6 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            Личная информация
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Имя -->
            <div>
                <x-auth.input 
                    wire:model="guest_name"
                    label="Имя *"
                    icon="user"
                    required
                    placeholder="Введите ваше имя"
                    :error="$errors->first('guest_name')"
                />
            </div>
            
            <!-- Email -->
            <div>
                <x-auth.input 
                    wire:model="guest_email"
                    label="Email *"
                    type="email"
                    icon="email"
                    required
                    placeholder="your@email.com"
                    :error="$errors->first('guest_email')"
                />
            </div>
            
            <!-- Телефон -->
            <div class="md:col-span-2">
                <x-auth.input 
                    wire:model="guest_phone"
                    label="Телефон"
                    type="tel"
                    icon="phone"
                    placeholder="+7 (XXX) XXX-XX-XX"
                    :error="$errors->first('guest_phone')"
                />
            </div>
        </div>
    </div>
</div>