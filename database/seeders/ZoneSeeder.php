<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ZoneSeeder extends Seeder
{
    public function run(): void
    {
        // получаем id мест
        $places = DB::table('places')->pluck('id', 'name');

        if ($places->isEmpty()) {
            return;
        }

        DB::table('zones')->insert([
            [
                'place_id'   => $places['Центральный клуб'],
                'name'       => 'VIP зал',
                'price_coef' => 1.5,
            ],
            [
                'place_id'   => $places['Центральный клуб'],
                'name'       => 'Обычный зал',
                'price_coef' => 1.0,
            ],
            [
                'place_id'   => $places['Бильярд на окраине'],
                'name'       => 'Большой зал',
                'price_coef' => 1.2,
            ],
        ]);
    }
}