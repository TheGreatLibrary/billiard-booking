<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\PriceRuleController;

Route::view('/', 'welcome');

// Публичные маршруты для обычных пользователей
Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Маршруты бронирований для обычных пользователей
Route::middleware(['auth'])->group(function () {
    Route::resource('bookings', BookingController::class);
});


// 👇 ЕДИНЫЙ БЛОК АДМИНСКИХ МАРШРУТОВ
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // Главная страница админки

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/stats', [DashboardController::class, 'stats'])->name('stats');
    Route::get('/payments/statistics', [PaymentController::class, 'statistics'])->name('payments.statistics');


Route::resource('bookings', App\Http\Controllers\Admin\BookingController::class);
    
    // Добавьте эти маршруты для AJAX
    Route::get('/bookings/zones/{place}', [App\Http\Controllers\Admin\BookingController::class, 'getZones'])
        ->name('bookings.zones');
    Route::get('/bookings/tables/{zone}', [App\Http\Controllers\Admin\BookingController::class, 'getTables'])
        ->name('bookings.tables');
    // Все CRUD маршруты для админки
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
    Route::resource('bookings', App\Http\Controllers\Admin\BookingController::class);
    Route::resource('orders', App\Http\Controllers\Admin\OrderController::class);
    Route::resource('payments', App\Http\Controllers\Admin\PaymentController::class);
    Route::resource('product-types', App\Http\Controllers\Admin\ProductTypeController::class);
    Route::resource('product-models', App\Http\Controllers\Admin\ProductModelController::class);
    Route::resource('places', App\Http\Controllers\Admin\PlaceController::class);
    Route::resource('zones', App\Http\Controllers\Admin\ZoneController::class);
    Route::resource('price-rules', App\Http\Controllers\Admin\PriceRuleController::class);
    Route::resource('resources', App\Http\Controllers\Admin\ResourceController::class);




    // Дополнительные маршруты изменения статусов
    Route::post('/bookings/{booking}/change-status', [AdminBookingController::class, 'changeStatus'])
        ->name('bookings.change-status');
    Route::post('/orders/{order}/change-status', [OrderController::class, 'changeStatus'])
        ->name('orders.change-status');
});



require __DIR__.'/auth.php';