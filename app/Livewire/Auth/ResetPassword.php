<?php
// app/Livewire/Auth/ResetPassword.php

namespace App\Livewire\Auth;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;

#[Layout('layouts.guest')]
class ResetPassword extends Component
{
    #[Locked]
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';


    public function mount(string $token): void
    {
        $this->token = $token;
        $this->email = request()->string('email');
    }


    protected function rules(): array
    {
        return [
            'token' => ['required'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ];
    }


    protected function messages(): array
    {
        return [
            'email.required' => 'Email обязателен для заполнения',
            'email.email' => 'Введите корректный email адрес',
            'password.required' => 'Пароль обязателен для заполнения',
            'password.confirmed' => 'Пароли не совпадают',
            'password.min' => 'Пароль должен содержать минимум 8 символов',
        ];
    }


    public function resetPassword(): void
    {
        $this->validate();

        $status = Password::reset(
            $this->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) {
                $user->forceFill([
                    'password' => Hash::make($this->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status != Password::PASSWORD_RESET) {
            $this->addError('email', $this->getErrorMessage($status));
            return;
        }

        Session::flash('status', 'Пароль успешно изменен! Теперь вы можете войти с новым паролем.');

        $this->redirectRoute('login', navigate: true);
    }


    private function getErrorMessage(string $status): string
    {
        return match($status) {
            Password::INVALID_TOKEN => 'Ссылка для сброса пароля устарела или недействительна',
            Password::INVALID_USER => 'Пользователь с таким email не найден',
            Password::RESET_THROTTLED => 'Слишком много попыток. Попробуйте позже',
            default => 'Произошла ошибка. Попробуйте еще раз',
        };
    }


    public function render()
    {
        return view('livewire.auth.reset-password');
    }
}