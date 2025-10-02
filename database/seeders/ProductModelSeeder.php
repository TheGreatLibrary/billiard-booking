<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductModelSeeder extends Seeder
{
    public function run(): void
    {
        $typeIds = DB::table('product_types')->pluck('id', 'name'); // ['table' => id, 'equipment' => id]
        if ($typeIds->isEmpty()) {
            return;
        }

        DB::table('product_models')->insert([
            [
                'product_type_id' => $typeIds['table'] ?? null,
                'name'            => 'Русская пирамида 12ft',
                'base_price_hour' => 900,   // 900 ₽/час
                'base_price_each' => null,
            ],
            [
                'product_type_id' => $typeIds['table'] ?? null,
                'name'            => 'Пул 9ft',
                'base_price_hour' => 700,   // 700 ₽/час
                'base_price_each' => null,
            ],
            [
                'product_type_id' => $typeIds['table'] ?? null,
                'name'            => 'Снукер 12ft',
                'base_price_hour' => 1000,  // 1000 ₽/час
                'base_price_each' => null,
            ],

            [
                'product_type_id' => $typeIds['equipment'] ?? null,
                'name'            => 'Кий прокат',
                'base_price_hour' => null,
                'base_price_each' => 100,   // 150 ₽/шт
            ],
            [
                'product_type_id' => $typeIds['equipment'] ?? null,
                'name'            => 'Мел',
                'base_price_hour' => null,
                'base_price_each' => 500,    // 50 ₽/шт
            ],
            [
                'product_type_id' => $typeIds['equipment'] ?? null,
                'name'            => 'Перчатка',
                'base_price_hour' => null,
                'base_price_each' => 200,   // 200 ₽/шт
            ],
        ]);
    }
}