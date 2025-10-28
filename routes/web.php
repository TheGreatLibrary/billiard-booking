<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\PriceRuleController;
use App\Http\Controllers\Admin\ProductTypeController;
use App\Http\Controllers\Admin\ProductModelController;
use App\Http\Controllers\Admin\PlaceController;
use App\Http\Controllers\Admin\ZoneController;
use App\Http\Controllers\Admin\ResourceController;

// Главная страница
Route::view('/', 'welcome')->name('home');

// Пользовательские страницы
Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('profile', 'profile')->name('profile');

    // Обновление профиля
    Route::patch('profile', function (Request $request) {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users,phone,' . $user->id,
            'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);

        return back()->with('success', 'Данные профиля обновлены!');
    })->name('profile.update');

    // Обновление пароля
    Route::put('password', function (Request $request) {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        auth()->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Пароль успешно изменён!');
    })->name('password.update');
});

// ============================================
// АДМИНСКИЕ МАРШРУТЫ
// ============================================
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/stats', [DashboardController::class, 'stats'])->name('stats');

    // AJAX маршруты (ПЕРЕД resource!)
    Route::get('/bookings/zones/{place}', [AdminBookingController::class, 'getZones'])
        ->name('bookings.zones');
    Route::get('/bookings/tables/{zone}', [AdminBookingController::class, 'getTables'])
        ->name('bookings.tables');
    Route::get('/bookings/equipment/{place}', [AdminBookingController::class, 'getEquipment'])
    ->name('bookings.equipment');

    // CRUD маршруты
    Route::resource('users', UserController::class);
    Route::resource('bookings', AdminBookingController::class);
    Route::resource('orders', OrderController::class);
    Route::resource('payments', PaymentController::class);
    Route::resource('product-types', ProductTypeController::class);
    Route::resource('product-models', ProductModelController::class);
    Route::resource('places', PlaceController::class);
    Route::resource('zones', ZoneController::class);
    Route::resource('price-rules', PriceRuleController::class);
    Route::resource('resources', ResourceController::class);

    // Дополнительные маршруты
    Route::get('/payments/statistics', [PaymentController::class, 'statistics'])->name('payments.statistics');
    Route::post('/bookings/{booking}/change-status', [AdminBookingController::class, 'changeStatus'])
        ->name('bookings.change-status');
    Route::post('/orders/{order}/change-status', [OrderController::class, 'changeStatus'])
        ->name('orders.change-status');
});

require __DIR__.'/auth.php';