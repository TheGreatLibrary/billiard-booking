<?php
// app/Livewire/Auth/VerifyEmail.php

namespace App\Livewire\Auth;

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\View\View;

#[Layout('layouts.guest')]
class VerifyEmail extends Component
{

    public function sendVerification(): void
    {
        $user = Auth::user();
        
        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
        Session::flash('message', 'Новое письмо для подтверждения отправлено на ваш email');
    }


    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }


    public function render(): View
    {
        return view('livewire.auth.verify-email');
    }
}