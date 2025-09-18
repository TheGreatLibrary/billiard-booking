<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // справочники
        $this->call([
            RoleSeeder::class,
            ProductTypeSeeder::class,
        ]);

        // ⚠️ ВАЖНО: НЕ вызывать User::factory(…) тут — именно это и сыпало ошибку

        // создаём админа с телефоном
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@example.com'],
            [
                'name'              => 'Admin',
                'phone'             => '79990000001',     // есть phone!
                'password'          => Hash::make('change_me'),
                'email_verified_at' => now(),
                'remember_token'    => Str::random(10),
                'created_at'        => now(),
                'updated_at'        => now(),
            ]
        );

        // навешиваем роль admin
        $userId = DB::table('users')->where('email', 'admin@example.com')->value('id');
        $roleId = DB::table('roles')->where('name', 'admin')->value('id');
        if ($userId && $roleId) {
            DB::table('role_user')->updateOrInsert([
                'user_id' => $userId,
                'role_id' => $roleId,
            ], []);
        }
    }
}