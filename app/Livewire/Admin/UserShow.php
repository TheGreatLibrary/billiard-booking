<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;

class UserShow extends Component
{
    public $user;

    public function mount($id)
    {
        $this->user = User::with('roles')->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.admin.user-show', [
            'user' => $this->user,
        ])->layout('admin.layout.app-livewire', [
            'title' => "Просмотр пользователя #{$this->user->id}",
        ]);
    }
}
