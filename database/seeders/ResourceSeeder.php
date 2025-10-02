<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ResourceSeeder extends Seeder
{
    public function run(): void
    {
        $zoneIds   = DB::table('zones')->pluck('id', 'name');              // ['VIP зал' => 1, ...]
        $modelIds  = DB::table('product_models')->pluck('id', 'name');     // ['Пул 9ft' => 2, ...]
        $stateIds  = DB::table('state_product')->pluck('id', 'name');      // ['active' => 1, ...]

        if ($zoneIds->isEmpty() || $modelIds->isEmpty() || $stateIds->isEmpty()) {
            return;
        }

        $activeId = $stateIds['active'] ?? $stateIds->first();

        $rows = [];

        // Пример для «VIP зал»
        if (isset($zoneIds['VIP зал'])) {
            $vip = $zoneIds['VIP зал'];
            if (isset($modelIds['Русская пирамида 12ft'])) {
                $rows[] = [
                    'model_id' => $modelIds['Русская пирамида 12ft'],
                    'zone_id'  => $vip,
                    'code'     => 'VIP-01',
                    'state_id' => $activeId,
                    'note'     => null,
                ];
                $rows[] = [
                    'model_id' => $modelIds['Русская пирамида 12ft'],
                    'zone_id'  => $vip,
                    'code'     => 'VIP-02',
                    'state_id' => $activeId,
                    'note'     => null,
                ];
            }
        }

        // Пример для «Обычный зал»
        if (isset($zoneIds['Обычный зал'])) {
            $regular = $zoneIds['Обычный зал'];
            if (isset($modelIds['Пул 9ft'])) {
                foreach (['A-01','A-02','A-03'] as $code) {
                    $rows[] = [
                        'model_id' => $modelIds['Пул 9ft'],
                        'zone_id'  => $regular,
                        'code'     => $code,
                        'state_id' => $activeId,
                        'note'     => null,
                    ];
                }
            }
            if (isset($modelIds['Снукер 12ft'])) {
                $rows[] = [
                    'model_id' => $modelIds['Снукер 12ft'],
                    'zone_id'  => $regular,
                    'code'     => 'SN-01',
                    'state_id' => $activeId,
                    'note'     => null,
                ];
            }
        }

        // Если зон/названий меньше — всё равно вставим то, что есть
        if (!empty($rows)) {
            DB::table('resources')->insert($rows);
        }
    }
}
