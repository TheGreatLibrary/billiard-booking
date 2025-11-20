<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ResourceSeeder extends Seeder
{
    public function run(): void
    {
        $placeIds  = DB::table('places')->pluck('id', 'name');             // ['Клуб "Шар"' => 1, ...]
        $zoneIds   = DB::table('zones')->pluck('id', 'name');              // ['VIP зал' => 1, ...]
        $modelIds  = DB::table('product_models')->pluck('id', 'name');     // ['Пул 9ft' => 2, ...]
        $stateIds  = DB::table('state_product')->pluck('id', 'name');      // ['active' => 1, ...]

        if ($placeIds->isEmpty() || $zoneIds->isEmpty() || $modelIds->isEmpty() || $stateIds->isEmpty()) {
            return;
        }

        $activeId = $stateIds['active'] ?? $stateIds->first();
        $defaultPlaceId = $placeIds->first(); // Используем первое место по умолчанию

        $rows = [];

        // Пример для «VIP зал»
        if (isset($zoneIds['VIP зал'])) {
            $vip = $zoneIds['VIP зал'];
            if (isset($modelIds['Русская пирамида 12ft'])) {
                $rows[] = [
                    'model_id' => $modelIds['Русская пирамида 12ft'],
                    'place_id' => $defaultPlaceId,
                    'zone_id'  => $vip,
                    'code'     => 'VIP-01',
                    'state_id' => $activeId,
                    'note'     => null,
                    'grid_x'   => 2,
                    'grid_y'   => 2,
                    'grid_width' => 3,
                    'grid_height' => 2,
                    'rotation' => 0,
                ];
                $rows[] = [
                    'model_id' => $modelIds['Русская пирамида 12ft'],
                    'place_id' => $defaultPlaceId,
                    'zone_id'  => $vip,
                    'code'     => 'VIP-02',
                    'state_id' => $activeId,
                    'note'     => null,
                    'grid_x'   => 6,
                    'grid_y'   => 2,
                    'grid_width' => 3,
                    'grid_height' => 2,
                    'rotation' => 0,
                ];
            }
        }

        // Пример для «Обычный зал»
        if (isset($zoneIds['Обычный зал'])) {
            $regular = $zoneIds['Обычный зал'];
            if (isset($modelIds['Пул 9ft'])) {
                $codes = ['A-01','A-02','A-03'];
                foreach ($codes as $index => $code) {
                    $rows[] = [
                        'model_id' => $modelIds['Пул 9ft'],
                        'place_id' => $defaultPlaceId,
                        'zone_id'  => $regular,
                        'code'     => $code,
                        'state_id' => $activeId,
                        'note'     => null,
                        'grid_x'   => 2 + ($index * 3),
                        'grid_y'   => 5,
                        'grid_width' => 2,
                        'grid_height' => 1,
                        'rotation' => 0,
                    ];
                }
            }
            if (isset($modelIds['Снукер 12ft'])) {
                $rows[] = [
                    'model_id' => $modelIds['Снукер 12ft'],
                    'place_id' => $defaultPlaceId,
                    'zone_id'  => $regular,
                    'code'     => 'SN-01',
                    'state_id' => $activeId,
                    'note'     => null,
                    'grid_x'   => 12,
                    'grid_y'   => 5,
                    'grid_width' => 3,
                    'grid_height' => 2,
                    'rotation' => 0,
                ];
            }
        }

        // Если зон/названий меньше — всё равно вставим то, что есть
        if (!empty($rows)) {
            DB::table('resources')->insert($rows);
        }
    }
}