<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BookingController;
Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// 👇 ВРЕМЕННО - простые маршруты для тестирования
// Добавьте после существующих маршрутов
Route::middleware(['auth'])->group(function () {
    Route::resource('bookings', \App\Http\Controllers\BookingController::class);
});

Route::prefix('admin')->name('admin.')->group(function () {
    // Главная страница админки
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware(['auth', 'admin'])
        ->name('dashboard');

    // Статистика API
    Route::get('/stats', [DashboardController::class, 'stats'])
        ->middleware(['auth', 'admin'])
        ->name('stats');

    // CRUD маршруты для админки
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::resource('/users', UserController::class);
        Route::resource('/bookings', AdminBookingController::class);
        Route::resource('/orders', OrderController::class);
        Route::resource('/payments', PaymentController::class);
    });
});

require __DIR__.'/auth.php';