<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Admin\{
    DashboardController,
    UserController,
    BookingController as AdminBookingController,
    OrderController,
    PaymentController,
    PriceRuleController,
    HallController,
    ProductTypeController,
    ProductModelController,
    PlaceController,
    ZoneController,
    ResourceController
};

// === Публичная часть ===
Route::view('/', 'welcome');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('profile', 'profile')->name('profile');

    // Бронирования для пользователей
    Route::resource('bookings', BookingController::class);
});

// === Админ-панель ===
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {

    // --- Главная и статистика ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/stats', [DashboardController::class, 'stats'])->name('stats');
    Route::get('/payments/statistics', [PaymentController::class, 'statistics'])->name('payments.statistics');

    // --- Управление бронированиями ---
    Route::resource('bookings', AdminBookingController::class);
    Route::post('/bookings/{booking}/change-status', [AdminBookingController::class, 'changeStatus'])->name('bookings.change-status');

    // AJAX-запросы для зон и столов
    Route::get('/bookings/zones/{place}', [AdminBookingController::class, 'getZones'])->name('bookings.zones');
    Route::get('/bookings/tables/{zone}', [AdminBookingController::class, 'getTables'])->name('bookings.tables');

    // --- Другие CRUD ---
    Route::resource('users', UserController::class);
    Route::resource('orders', OrderController::class);
    Route::post('/orders/{order}/change-status', [OrderController::class, 'changeStatus'])->name('orders.change-status');
    Route::resource('payments', PaymentController::class);
    Route::resource('product-types', ProductTypeController::class);
    Route::resource('product-models', ProductModelController::class);
    Route::resource('places', PlaceController::class);
    Route::resource('zones', ZoneController::class);
    Route::resource('price-rules', PriceRuleController::class);
    Route::resource('resources', ResourceController::class);

    // --- Редактор зала ---
// === ХОЛЛ / КАРТА ЗАЛА ===
Route::get('/', [HallController::class, 'index'])->name('hall.index');
Route::get('/hall/resources', [HallController::class, 'resources']);
Route::post('/hall/add/{id}', [HallController::class, 'add']);
Route::post('/hall/update-position/{id}', [HallController::class, 'updatePosition']);
Route::post('/hall/remove/{id}', [HallController::class, 'remove']);

});

require __DIR__.'/auth.php';
