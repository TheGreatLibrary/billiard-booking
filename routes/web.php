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


use App\Livewire\Admin\HallEditor;

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
    
    /**
     * !!!!!!!!!!!!!! НОВЫЕ МАРШРУТЫ ДЛЯ LIVEWIRE !!!!!!!!!!
     */
    Route::get('/dashboard', \App\Livewire\Admin\AdminDashboard::class)->name('dashboard');

    Route::get('/bookings', \App\Livewire\Admin\BookingList::class)->name('bookings.index');
    Route::get('/bookings/create', \App\Livewire\Admin\BookingCreate::class)->name('bookings.create');
    Route::get('/bookings/{booking}/edit', \App\Livewire\Admin\BookingEditForm::class)->name('bookings.edit');
    Route::get('/bookings/{booking}', \App\Livewire\Admin\BookingShow::class)->name('bookings.show'); 
    Route::get('/bookings/{booking}/pay', \App\Livewire\Admin\BookingPay::class) ->name('bookings.pay');

    Route::get('/resources',  \App\Livewire\Admin\ResourceList::class)->name('resources.index');
    Route::get('/resources/create', \App\Livewire\Admin\ResourceForm::class)->name('resources.create');
    Route::get('/resources/{id}/edit', \App\Livewire\Admin\ResourceForm::class)->name('resources.edit');
    Route::get('/users', \App\Livewire\Admin\UserList::class)->name('users.index');
    Route::get('/users/create', \App\Livewire\Admin\UserForm::class)->name('users.create');
    Route::get('/users/{id}/edit', \App\Livewire\Admin\UserForm::class)->name('users.edit');
    Route::get('/users/{id}', \App\Livewire\Admin\UserShow::class)->name('users.show');
    Route::get('/zones', \App\Livewire\Admin\ZoneList::class)->name('zones.index');
    Route::get('/zones/create', \App\Livewire\Admin\ZoneForm::class)->name('zones.create');
    Route::get('/zones/{id}/edit', \App\Livewire\Admin\ZoneForm::class)->name('zones.edit');
    Route::get('/product-types', \App\Livewire\Admin\ProductTypeList::class)->name('product-types.index');
    Route::get('/product-types/create', \App\Livewire\Admin\ProductTypeForm::class)->name('product-types.create');
    Route::get('/product-types/{id}/edit', \App\Livewire\Admin\ProductTypeForm::class)->name('product-types.edit');

    Route::get('/places', \App\Livewire\Admin\PlaceIndex::class)->name('places.index');
    Route::get('/places/create', \App\Livewire\Admin\PlaceCreate::class)->name('places.create');
    Route::get('/places/{place}/edit', \App\Livewire\Admin\PlaceEdit::class)->name('places.edit');

    Route::get('/halls-editor', HallEditor::class)->name('halls.editor');

    /**
     * !!!!!!!!!!!!!!! КОНЕЦ НОВЫХ МАРШРУТОВ !!!!!!!!!!!!
     */

    // CRUD маршруты
//  Route::resource('users', UserController::class);
//  Route::resource('product-types', ProductTypeController::class);
    Route::resource('product-models', ProductModelController::class);
   // Route::resource('places', PlaceController::class);
//  Route::resource('zones', ZoneController::class);
    Route::resource('price-rules', PriceRuleController::class);
 //   Route::resource('resources', ResourceController::class);

});

require __DIR__.'/auth.php';
