<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Validation\Rule;

class UserForm extends Component
{
    public $userId;
    public $name;
    public $email;
    public $phone;
    public $password;
    public $roles = [];

    public $availableRoles = [];

    public function mount(UserService $service, $id = null)
    {
        $this->availableRoles = $service->getAvailableRoles(auth()->user());

        if ($id) {
            $user = User::findOrFail($id);
            $this->userId = $user->id;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->phone = $user->phone;
            $this->roles = $user->roles->pluck('id')->toArray();
        }
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->userId),
            ],
            'phone' => [
                'required',
                'string',
                Rule::unique('users', 'phone')->ignore($this->userId),
            ],
            'password' => [$this->userId ? 'nullable' : 'required', 'min:8'],
            'roles' => ['array'],
        ];
    }

    public function messages()
    {
        return [
            'email.unique' => 'Такой email уже существует.',
            'phone.unique' => 'Такой номер телефона уже зарегистрирован.',
            'password.min' => 'Пароль должен содержать минимум 8 символов.',
        ];
    }

    public function save(UserService $service)
    {
        $validated = $this->validate();

        if ($this->userId) {
            $user = User::findOrFail($this->userId);
            $service->updateUser($user, $validated);
            session()->flash('success', 'Пользователь успешно обновлён.');
        } else {
            $service->createUser($validated);
            session()->flash('success', 'Пользователь успешно создан.');
        }

        return redirect()->route('admin.users.index');
    }

    public function render()
    {
        $title = $this->userId ? 'Редактирование пользователя' : 'Создание пользователя';

        return view('livewire.admin.user-form')
            ->layout('admin.layout.app-livewire', compact('title'));
    }
}
