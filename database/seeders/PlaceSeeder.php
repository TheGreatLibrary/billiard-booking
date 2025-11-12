<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlaceSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('places')->insert([
            [
                'name'        => 'Центральный клуб',
                'address'     => 'ул. Ленина, 10',
                'description' => 'Главный клуб в центре города',
                'grid_width'  => 20,  // 20 клеток по горизонтали
                'grid_height' => 10,  // 10 клеток по вертикали
                'hall_image'  => null, // опционально можно добавить картинку
            ],
            [
                'name'        => 'Бильярд на окраине',
                'address'     => 'ул. Рабочая, 25',
                'description' => 'Небольшой уютный клуб с демократичными ценами',
                'grid_width'  => 15,  // меньший зал
                'grid_height' => 8,
                'hall_image'  => null,
            ],
        ]);
    }
}