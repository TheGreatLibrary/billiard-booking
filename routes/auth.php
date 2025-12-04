<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Livewire\Auth\RegisterForm;
use App\Livewire\Auth\LoginForm;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\ResetPassword;


Route::middleware('guest')->group(function () {
    // Регистрация
    Route::get('/register', RegisterForm::class)->name('register');
    
    // Вход
    Route::get('/login', LoginForm::class)->name('login');
    
    // Восстановление пароля
    Route::get('/forgot-password', ForgotPassword::class)->name('password.request');
    Route::get('/reset-password/{token}', ResetPassword::class)->name('password.reset');
});


Route::middleware('auth')->group(function () {
    // Выход из системы
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
    
    // Подтверждение email
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    
    // Уведомление о подтверждении email
    Volt::route('verify-email', 'pages.auth.verify-email')
        ->name('verification.notice');
    
    // Подтверждение пароля
    Volt::route('confirm-password', 'pages.auth.confirm-password')
        ->name('password.confirm');
});