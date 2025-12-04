<?php
// app/Livewire/Auth/LoginForm.php

namespace App\Livewire\Auth;

use App\Livewire\Forms\LoginForm as LoginFormObject;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\View\View;

#[Layout('layouts.guest')]
class LoginForm extends Component
{
    public LoginFormObject $form;


    public function login(): void
    {
        $this->form->validate();

        $this->form->authenticate();

        Session::regenerate();

        $user = auth()->user();
        
        // Определяем куда редиректить на основе роли
        $redirectTo = $user->hasRole('admin') 
            ? '/admin/dashboard'
            : route('dashboard', absolute: false);

        $this->redirectIntended(default: $redirectTo, navigate: true);
    }


    public function render(): View
    {
        return view('livewire.auth.login-form');
    }
}