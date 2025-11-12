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
                'place_id'    => $places['Центральный клуб'],
                'name'        => 'VIP зал',
                'price_coef'  => 1.5,
                'coordinates' => json_encode([
                    ['x' => 0, 'y' => 0],
                    ['x' => 10, 'y' => 0],
                    ['x' => 10, 'y' => 5],
                    ['x' => 0, 'y' => 5],
                ]), // Полигон VIP зоны (верхняя половина)
                'color'       => '#9333EA', // Фиолетовый для VIP
            ],
            [
                'place_id'    => $places['Центральный клуб'],
                'name'        => 'Обычный зал',
                'price_coef'  => 1.0,
                'coordinates' => json_encode([
                    ['x' => 0, 'y' => 5],
                    ['x' => 20, 'y' => 5],
                    ['x' => 20, 'y' => 10],
                    ['x' => 0, 'y' => 10],
                ]), // Полигон обычной зоны (нижняя половина)
                'color'       => '#3B82F6', // Синий для обычной
            ],
            [
                'place_id'    => $places['Бильярд на окраине'],
                'name'        => 'Большой зал',
                'price_coef'  => 1.2,
                'coordinates' => json_encode([
                    ['x' => 0, 'y' => 0],
                    ['x' => 15, 'y' => 0],
                    ['x' => 15, 'y' => 8],
                    ['x' => 0, 'y' => 8],
                ]), // Весь зал
                'color'       => '#10B981', // Зеленый
            ],
        ]);
    }
}