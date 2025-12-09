<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $user = auth()->user();
        
        // Определяем куда редиректить based on role
        $redirectTo = $user->hasRole('admin') 
            ? '/admin/dashboard'
            : route('dashboard', absolute: false);

        $this->redirectIntended(default: $redirectTo, navigate: true);
    }
}; ?>

<!-- Session Status -->
<x-auth-session-status class="mb-4" :status="session('status')" />

<form wire:submit="login">
    <!-- Email Address -->
    <div>
        <x-input-label for="email" :value="__('Email')" />
        <x-text-input 
            wireModel="form.email" 
            id="email" 
            class="block mt-1 w-full" 
            type="email" 
            name="email" 
            required 
            autofocus 
            autocomplete="username" 
            error="{!! $errors->first('form.email') ?: null !!}"
        />
        <!-- Можно удалить <x-input-error>, если ошибка передается в text-input -->
    </div>

    <!-- Password -->
    <div class="mt-4">
        <x-input-label for="password" :value="__('Password')" />

        <x-text-input 
            wireModel="form.password" 
            id="password" 
            class="block mt-1 w-full"
            type="password"
            name="password"
            required 
            autocomplete="current-password"
            error="{!! $errors->first('form.password') ?: null !!}"
        />
    </div>

    <!-- Remember Me -->
    <div class="block mt-4">
        <label for="remember" class="inline-flex items-center">
            <input wire:model="form.remember" id="remember" type="checkbox" 
                   class="rounded border-gray-300 text-amber-600 shadow-sm focus:ring-amber-500">
            <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
        </label>
    </div>

    <div class="flex items-center justify-end mt-4">
        @if (Route::has('password.request'))
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500" 
               href="{{ route('password.request') }}" 
               wire:navigate>
                {{ __('Forgot your password?') }}
            </a>
        @endif

        <x-primary-button class="ms-3">
            {{ __('Log in') }}
        </x-primary-button>
    </div>
</form>
