<?php
// app/Livewire/Auth/ForgotPassword.php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\View\View;

#[Layout('layouts.guest')]
class ForgotPassword extends Component
{
    public string $email = '';


    protected function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'exists:users,email'],
        ];
    }


    protected function messages(): array
    {
        return [
            'email.required' => 'Email обязателен для заполнения',
            'email.email' => 'Введите корректный email адрес',
            'email.exists' => 'Пользователь с таким email не найден',
        ];
    }

 
    public function sendPasswordResetLink(): void
    {
        $this->validate();

        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', $this->getErrorMessage($status));
            return;
        }

        $this->reset('email');
        session()->flash('status', $this->getSuccessMessage($status));
    }


    private function getErrorMessage(string $status): string
    {
        return match($status) {
            Password::INVALID_USER => 'Пользователь с таким email не найден',
            Password::RESET_THROTTLED => 'Слишком много попыток. Попробуйте позже',
            default => 'Произошла ошибка. Попробуйте еще раз',
        };
    }


    private function getSuccessMessage(string $status): string
    {
        return 'Ссылка для восстановления пароля отправлена на ваш email. Проверьте почту.';
    }


    public function render(): View
    {
        return view('livewire.auth.forgot-password');
    }
}