<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function getAvailableRoles($user)
    {
        if ($user->hasRole('admin')) {
            return Role::where('name', '!=', 'admin')->get();
        }

        if ($user->hasRole('moderator')) {
            return Role::where('name', 'user')->get();
        }

        return collect();
    }

    public function createUser(array $data)
    {
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'],
            'password' => Hash::make($data['password']),
        ]);

        if (!empty($data['roles'])) {
            $user->roles()->sync($data['roles']);
        }

        return $user;
    }

    public function updateUser(User $user, array $data)
    {
        $user->update([
            'name'  => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
        ]);

        if (!empty($data['roles'])) {
            $user->roles()->sync($data['roles']);
        }

        return $user;
    }
}
