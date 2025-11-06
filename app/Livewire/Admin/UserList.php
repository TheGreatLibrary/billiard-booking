<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;

class UserList extends Component
{
    use WithPagination;

    public $search = '';

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        session()->flash('success', 'Пользователь удалён');
    }

    public function render()
    {
        $users = User::query()
            ->where('name', 'like', "%{$this->search}%")
            ->orWhere('email', 'like', "%{$this->search}%")
            ->latest()
            ->paginate(10);

        return view('livewire.admin.user-list', compact('users'))
            ->layout('admin.layout.app-livewire', [
                'title' => 'Пользователи',
            ]);
    }
}
