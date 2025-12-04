<?php
// app/Livewire/Auth/ConfirmPassword.php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\View\View;

#[Layout('layouts.guest')]
class ConfirmPassword extends Component
{
    public string $password = '';

    protected function rules(): array
    {
        return [
            'password' => ['required', 'string'],
        ];
    }


    protected function messages(): array
    {
        return [
            'password.required' => 'Пароль обязателен для заполнения',
        ];
    }


    public function confirmPassword(): void
    {
        $this->validate();

        if (! Auth::guard('web')->validate([
            'email' => Auth::user()->email,
            'password' => $this->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => 'Введен неверный пароль. Попробуйте еще раз.',
            ]);
        }

        session(['auth.password_confirmed_at' => time()]);

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }


    public function render(): View
    {
        return view('livewire.auth.confirm-password');
    }
}