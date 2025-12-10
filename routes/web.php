<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

use \App\Livewire\Admin\{AdminDashboard, 
    BookingList, BookingCreate as AdminBookingCreate, BookingShow, BookingPay, 
    ResourceList, ResourceForm, 
    UserList, UserForm, UserShow,
    ZoneList, ZoneForm, ZoneEditor,
    ProductTypeList, ProductTypeForm, TableEditor,
    PlaceIndex, PlaceFormCreate, PlaceFormEdit,
    ProductModelForm, ProductModelList,
    PriceRuleForm, PriceRuleList,
};
use App\Livewire\UserDashboard;
use App\Livewire\BookingCreate;
use App\Livewire\Guest\BookingCreate as GuestBookingCreate;
use App\Livewire\Profile;
use App\Livewire\Auth\RegisterForm;
use App\Livewire\Auth\LoginForm;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Auth\VerifyEmail;
use App\Livewire\Auth\ConfirmPassword;



// Главная страница
Route::view('/', 'welcome')->name('home');

// ==================== АУТЕНТИФИКАЦИЯ (Гости) ====================
Route::middleware('guest')->group(function () {
    Route::get('/booking-guest', GuestBookingCreate::class)->name('booking.create');
    Route::get('/register', RegisterForm::class)->name('register');
    Route::get('/login', LoginForm::class)->name('login');
    Route::get('/forgot-password', ForgotPassword::class)->name('password.request');
    Route::get('/reset-password/{token}', ResetPassword::class)->name('password.reset');
});

// ==================== ПОЛЬЗОВАТЕЛЬ (Авторизован) ====================
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', UserDashboard::class)->name('dashboard');
    Route::get('/profile', Profile::class)->name('profile');
    Route::get('/booking', BookingCreate::class)->name('booking.create.auth');
    
    Route::patch('profile', function (Request $request) {
        // ... ваш код
    })->name('profile.update');
    
    Route::put('password', function (Request $request) {
        // ... ваш код
    })->name('password.update');
});

// ==================== ПОДТВЕРЖДЕНИЕ (Авторизован) ====================
Route::middleware('auth')->group(function () {
    Route::post('logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
    
    Route::get('verify-email/{id}/{hash}', function ($id, $hash) {
        // ... логика подтверждения email
    })->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    
    Route::get('/verify-email', VerifyEmail::class)->name('verification.notice');
    Route::get('/confirm-password', ConfirmPassword::class)->name('password.confirm');
});

// ==================== АДМИН (Авторизован + роль admin) ====================
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    
    Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
   
    Route::get('/price-rules', PriceRuleList::class)->name('price-rules.index');
    Route::get('/price-rules/create', PriceRuleForm::class)->name('price-rules.create');
    Route::get('/price-rules/{rule}/edit', PriceRuleForm::class)->name('price-rules.edit');

    Route::get('/product-models', ProductModelList::class)->name('product-models.index');
    Route::get('/product-models/create', ProductModelForm::class)->name('product-models.create');
    Route::get('/product-models/{model}/edit', ProductModelForm::class)->name('product-models.edit');
    
    Route::get('/bookings', BookingList::class)->name('bookings.index');
    Route::get('/bookings/create', AdminBookingCreate::class)->name('bookings.create');
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


