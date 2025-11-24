<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Http\Controllers\Admin\DashboardController;

use \App\Livewire\Admin\{AdminDashboard, 
    BookingList, BookingCreate, BookingEditForm, BookingShow, BookingPay, 
    ResourceList, ResourceForm, 
    UserList, UserForm, UserShow,
    ZoneList, ZoneForm, ZoneEditor,
    ProductTypeList, ProductTypeForm, TableEditor,
    PlaceIndex, PlaceFormCreate, PlaceFormEdit,
    ProductModelForm, ProductModelList,
    PriceRuleForm, PriceRuleList,
};
use \App\Livewire\UserDashboard;

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
    
    Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
   
    Route::get('/price-rules', PriceRuleList::class)->name('price-rules.index');
    Route::get('/price-rules/create', PriceRuleForm::class)->name('price-rules.create');
    Route::get('/price-rules/{rule}/edit', PriceRuleForm::class)->name('price-rules.edit');

    Route::get('/product-models', ProductModelList::class)->name('product-models.index');
    Route::get('/product-models/create', ProductModelForm::class)->name('product-models.create');
    Route::get('/product-models/{model}/edit', ProductModelForm::class)->name('product-models.edit');
    
    Route::get('/bookings', BookingList::class)->name('bookings.index');
    Route::get('/bookings/create', BookingCreate::class)->name('bookings.create');
    Route::get('/bookings/{booking}/edit', BookingEditForm::class)->name('bookings.edit');
    Route::get('/bookings/{booking}', BookingShow::class)->name('bookings.show'); 
    Route::get('/bookings/{booking}/pay', BookingPay::class) ->name('bookings.pay');

    Route::get('/resources',  ResourceList::class)->name('resources.index');
    Route::get('/resources/create', ResourceForm::class)->name('resources.create');
    Route::get('/resources/{id}/edit', ResourceForm::class)->name('resources.edit');

    Route::get('/users', UserList::class)->name('users.index');
    Route::get('/users/create', UserForm::class)->name('users.create');
    Route::get('/users/{id}/edit', UserForm::class)->name('users.edit');
    Route::get('/users/{id}', UserShow::class)->name('users.show');

    Route::get('/zones', ZoneList::class)->name('zones.index');
    Route::get('/zones/create', ZoneForm::class)->name('zones.create');
    Route::get('/zones/{id}/edit', ZoneForm::class)->name('zones.edit');
    Route::get('/zones-editor', ZoneEditor::class)->name('zones.editor');

    Route::get('/product-types', ProductTypeList::class)->name('product-types.index');
    Route::get('/product-types/create', ProductTypeForm::class)->name('product-types.create');
    Route::get('/product-types/{id}/edit', ProductTypeForm::class)->name('product-types.edit');
    Route::get('/tables-editor', TableEditor::class)->name('tables.editor');

    Route::get('/places', PlaceIndex::class)->name('places.index');
    Route::get('/places/create', PlaceFormCreate::class)->name('places.form.create');
    Route::get('/places/{place}/edit', PlaceFormEdit::class)->name('places.form.edit');
});

require __DIR__.'/auth.php';
