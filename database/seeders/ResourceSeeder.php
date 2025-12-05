<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ResourceSeeder extends Seeder
{
    public function run(): void
    {
        $placeIds  = DB::table('places')->pluck('id', 'name');
        $zoneIds   = DB::table('zones')->pluck('id', 'name');
        $modelIds  = DB::table('product_models')->pluck('id', 'name');
        $stateIds  = DB::table('state_product')->pluck('id', 'name');

        if ($placeIds->isEmpty() || $modelIds->isEmpty() || $stateIds->isEmpty()) {
            return;
        }

        $activeId = $stateIds['active'] ?? $stateIds->first();
        $defaultPlaceId = $placeIds->first();

        $rows = [];

        // ============================================
        // СТОЛЫ (type = 'table', quantity = 1)
        // ============================================
        
        // VIP зал
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
                    'quantity' => 1,
                    'type' => 'table'
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
                    'quantity' => 1,
                    'type' => 'table'
                ];
            }
        }

        // Обычный зал
        if (isset($zoneIds['Обычный зал'])) {
            $regular = $zoneIds['Обычный зал'];
            
            // Пул столы
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
                        'quantity' => 1,
                        'type' => 'table'
                    ];
                }
            }
            
            // Снукер стол
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
                    'quantity' => 1,
                    'type' => 'table'
                ];
            }
        }

        // ============================================
        // ИНВЕНТАРЬ (type = 'equipment', quantity > 1)
        // ============================================
        
        // Кии
        if (isset($modelIds['Кий стандартный'])) {
            $rows[] = [
                'model_id' => $modelIds['Кий стандартный'],
                'place_id' => $defaultPlaceId,
                'zone_id'  => null, // Инвентарь не привязан к зоне
                'code'     => 'CUE-STD',
                'state_id' => $activeId,
                'note'     => 'Обычные кии для клиентов',
                'grid_x'   => null, // Нет координат
                'grid_y'   => null,
                'grid_width' => 0,
                'grid_height' => 0,
                'rotation' => 0,
                'quantity' => 20, // 20 штук в наличии
                'type' => 'equipment'
            ];
        }

        if (isset($modelIds['Кий профессиональный'])) {
            $rows[] = [
                'model_id' => $modelIds['Кий профессиональный'],
                'place_id' => $defaultPlaceId,
                'zone_id'  => null,
                'code'     => 'CUE-PRO',
                'state_id' => $activeId,
                'note'     => 'Премиум кии',
                'grid_x'   => null,
                'grid_y'   => null,
                'grid_width' => 0,
                'grid_height' => 0,
                'rotation' => 0,
                'quantity' => 5, // 5 штук
                'type' => 'equipment'
            ];
        }

        // Мел
        if (isset($modelIds['Мел'])) {
            $rows[] = [
                'model_id' => $modelIds['Мел'],
                'place_id' => $defaultPlaceId,
                'zone_id'  => null,
                'code'     => 'CHALK',
                'state_id' => $activeId,
                'note'     => 'Мел для киев',
                'grid_x'   => null,
                'grid_y'   => null,
                'grid_width' => 0,
                'grid_height' => 0,
                'rotation' => 0,
                'quantity' => 50, // 50 штук
                'type' => 'equipment'
            ];
        }

        // Перчатки
        if (isset($modelIds['Перчатка'])) {
            $rows[] = [
                'model_id' => $modelIds['Перчатка'],
                'place_id' => $defaultPlaceId,
                'zone_id'  => null,
                'code'     => 'GLOVE',
                'state_id' => $activeId,
                'note'     => 'Перчатки для игры',
                'grid_x'   => null,
                'grid_y'   => null,
                'grid_width' => 0,
                'grid_height' => 0,
                'rotation' => 0,
                'quantity' => 15, // 15 штук
                'type' => 'equipment'
            ];
        }

        // Вставка данных
        if (!empty($rows)) {
            DB::table('resources')->insert($rows);
            
            $tableCount = collect($rows)->where('type', 'table')->count();
            $equipmentCount = collect($rows)->where('type', 'equipment')->count();
            
            $this->command->info("✅ Создано ресурсов:");
            $this->command->info("   📊 Столов: {$tableCount}");
            $this->command->info("   🎱 Инвентаря: {$equipmentCount}");
        }
    }
}