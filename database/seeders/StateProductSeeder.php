<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StateProductSeeder extends Seeder
{
    public function run(): void
    {
        // name уникален по миграции
        DB::table('state_product')->upsert([
            ['name' => 'active'],
            ['name' => 'maintenance'],
            ['name' => 'broken'],
        ], ['name']);
    }
}
