<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Список пользователей.
     */
    public function index()
    {
        $users = User::with('roles')->latest()->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Форма создания пользователя.
     */
    public function create()
    {
        $roles = $this->availableRoles();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Сохранение нового пользователя.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'phone'    => 'required|string|unique:users',
            'password' => 'required|min:8',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => bcrypt($request->password),
        ]);

        if ($request->has('roles')) {
            $allowed  = $this->availableRoles()->pluck('id')->toArray();
            $filtered = array_intersect($request->roles, $allowed);
            $user->roles()->sync($filtered);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Пользователь создан успешно');
    }

    /**
     * Просмотр профиля пользователя.
     */
    public function show(User $user)
    {
        $user->load('roles');
        return view('admin.users.show', compact('user'));
    }

    /**
     * Форма редактирования пользователя.
     */
    public function edit(User $user)
    {
        $roles = $this->availableRoles();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Обновление данных пользователя.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required|string|unique:users,phone,' . $user->id,
        ]);

        $user->update($request->only(['name', 'email', 'phone']));

        if ($request->has('roles')) {
            $allowed  = $this->availableRoles()->pluck('id')->toArray();
            $filtered = array_intersect($request->roles, $allowed);
            $user->roles()->sync($filtered);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Пользователь обновлен успешно');
    }

    /**
     * Удаление пользователя.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', 'Пользователь удален успешно');
    }

    /**
     * Возвращает коллекцию ролей, которые текущий авторизованный пользователь
     * может назначать другим.
     *
     * - Admin: может назначать все роли, КРОМЕ admin.
     * - Moderator: может назначать только user.
     * - Остальные: пусто.
     */
    protected function availableRoles()
    {
        $current = auth()->user();

        // Если текущий пользователь админ
        if ($current->hasRole('admin')) {
            return Role::where('name', '!=', 'admin')->get();
        }

        // Если текущий пользователь модератор
        if ($current->hasRole('moderator')) {
            return Role::where('name', 'user')->get();
        }

        // По умолчанию ничего
        return collect();
    }
}
