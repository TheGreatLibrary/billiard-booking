<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PriceRuleSeeder extends Seeder
{
    public function run(): void
    {
        $placeIds = DB::table('places')->pluck('id', 'name');   // ['Центральный клуб' => 1, ...]
        $zoneIds  = DB::table('zones')->pluck('id', 'name');    // ['VIP зал' => 2, ...]

        if ($placeIds->isEmpty()) {
            return;
        }

        $rules = [];

        // --- Центральный клуб, правило по умолчанию (все зоны, будни, день) ---
        if (isset($placeIds['Центральный клуб'])) {
            $rules[] = [
                'place_id' => $placeIds['Центральный клуб'],
                'zone_id'  => null,         // для всех зон
                'dow'      => null,         // любой день недели
                'time_from'=> '10:00',
                'time_to'  => '18:00',
                'kind'     => 'coef',
                'value'    => 1.0,          // базовая цена
                'active'   => true,
            ];

            // вечером коэффициент 1.2
            $rules[] = [
                'place_id' => $placeIds['Центральный клуб'],
                'zone_id'  => null,
                'dow'      => null,
                'time_from'=> '18:00',
                'time_to'  => '23:59',
                'kind'     => 'coef',
                'value'    => 1.2,
                'active'   => true,
            ];

            // выходные (сб=6, вс=0) — коэффициент 1.3
            foreach ([0, 6] as $dow) {
                $rules[] = [
                    'place_id' => $placeIds['Центральный клуб'],
                    'zone_id'  => null,
                    'dow'      => $dow,
                    'time_from'=> null,
                    'time_to'  => null,
                    'kind'     => 'coef',
                    'value'    => 1.3,
                    'active'   => true,
                ];
            }

            // VIP-зал отдельное правило: всегда дороже на 50%
            if (isset($zoneIds['VIP зал'])) {
                $rules[] = [
                    'place_id' => $placeIds['Центральный клуб'],
                    'zone_id'  => $zoneIds['VIP зал'],
                    'dow'      => null,
                    'time_from'=> null,
                    'time_to'  => null,
                    'kind'     => 'coef',
                    'value'    => 1.5,
                    'active'   => true,
                ];
            }
        }

        if (!empty($rules)) {
            DB::table('price_rules')->insert($rules);
        }
    }
}
