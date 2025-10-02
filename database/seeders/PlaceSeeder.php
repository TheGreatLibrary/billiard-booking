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
            ],
            [
                'name'        => 'Бильярд на окраине',
                'address'     => 'ул. Рабочая, 25',
                'description' => 'Небольшой уютный клуб с демократичными ценами',
            ],
        ]);
    }
}