<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;

class Profile extends Component
{
    public $name;
    public $phone;
    public $email;
    public $current_password;
    public $password;
    public $password_confirmation;

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->phone = $user->phone;
        $this->email = $user->email;
    }

    public function updateProfile()
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users,phone,' . $user->id,
            'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);

        session()->flash('success', 'Данные профиля обновлены!');
    }

    public function updatePassword()
    {
        $validated = $this->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        // Очищаем поля пароля
        $this->reset(['current_password', 'password', 'password_confirmation']);

        session()->flash('success', 'Пароль успешно изменён!');
    }

    public function render()
    {
        return view('livewire.User.profile')->layout('layouts.app');
    }
}