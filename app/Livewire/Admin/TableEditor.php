<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\{Place, Zone, Resource, ProductModel, ProductType, StateProduct};

class TableEditor extends Component
{
    // Выбранное место
    public $placeId = null;
    public $place = null;
    
    // Данные
    public $places = [];
    public $zones = [];
    public $tablesOnGrid = []; // Столы уже на карте
    public $tablesAvailable = []; // Доступные столы (не размещены)
    
    // Параметры сетки
    public $gridWidth = 20;
    public $gridHeight = 10;
    
    // Выбранный стол для размещения
    public $selectedTableId = null;
    
    // Выбранный стол на карте (для редактирования)
    public $selectedGridTableId = null;

    public function mount()
    {
        $this->places = Place::all();
        
        if ($this->places->count() === 1) {
            $this->placeId = $this->places->first()->id;
            $this->updatedPlaceId($this->placeId);
        }
    }

    public function updatedPlaceId($value)
    {
        if (!$value) {
            $this->reset(['place', 'zones', 'tablesOnGrid', 'tablesAvailable']);
            return;
        }
        
        $this->place = Place::findOrFail($value);
        $this->gridWidth = $this->place->grid_width ?? 20;
        $this->gridHeight = $this->place->grid_height ?? 10;
        
        $this->loadZones();
        $this->loadTables();
        $this->selectedTableId = null;
        $this->selectedGridTableId = null;
    }

    private function loadZones()
    {
        if (!$this->place) return;
        
        $this->zones = Zone::where('place_id', $this->place->id)
            ->get()
            ->map(fn($z) => [
                'id' => $z->id,
                'name' => $z->name,
                'color' => $z->color ?? '#3B82F6',
                'price_coef' => (float) $z->price_coef,
                'coordinates' => is_string($z->coordinates) 
                    ? json_decode($z->coordinates, true) 
                    : ($z->coordinates ?? []),
            ])
            ->toArray();
    }

    private function loadTables()
    {
        if (!$this->place) return;
        
        // Получаем ID типа "стол"
        $tableTypeId = ProductType::where('name', 'LIKE', '%стол%')
            ->orWhere('name', 'LIKE', '%table%')
            ->first()?->id;
        
        if (!$tableTypeId) {
            session()->flash('warning', 'Не найден тип продукта "Стол". Создайте его сначала.');
            return;
        }
        
        // Все столы этого места
        $allTables = Resource::where('place_id', $this->place->id)
            ->whereHas('model', function($q) use ($tableTypeId) {
                $q->where('product_type_id', $tableTypeId);
            })
            ->with(['model', 'zone', 'state'])
            ->get();
        
        // Разделяем на размещенные и доступные
        $this->tablesOnGrid = $allTables
            ->filter(fn($t) => !is_null($t->grid_x) && !is_null($t->grid_y))
            ->map(fn($t) => $this->mapTableData($t))
            ->values()
            ->toArray();
        
        $this->tablesAvailable = $allTables
            ->filter(fn($t) => is_null($t->grid_x) || is_null($t->grid_y))
            ->map(fn($t) => $this->mapTableData($t))
            ->values()
            ->toArray();
    }

    private function mapTableData($table)
    {
        return [
            'id' => $table->id,
            'code' => $table->code ?? 'N/A',
            'model_name' => $table->model->name ?? 'Unknown',
            'zone_id' => $table->zone_id,
            'zone_name' => $table->zone->name ?? 'Не назначена',
            'zone_color' => $table->zone->color ?? '#CCCCCC',
            'state' => $table->state->name ?? 'unknown',
            'grid_x' => $table->grid_x,
            'grid_y' => $table->grid_y,
            'grid_width' => $table->grid_width ?? 2,
            'grid_height' => $table->grid_height ?? 1,
            'rotation' => $table->rotation ?? 0,
        ];
    }

    /**
     * Выбрать стол из списка доступных
     */
    public function selectTable($tableId)
    {
        $this->selectedTableId = $tableId;
        $this->selectedGridTableId = null;
    }

    /**
     * Разместить стол на сетке
     */
    public function placeTable($gridX, $gridY)
    {
        if (!$this->selectedTableId) {
            session()->flash('error', 'Сначала выберите стол из списка');
            return;
        }
        
        $table = collect($this->tablesAvailable)->firstWhere('id', $this->selectedTableId);
        
        if (!$table) {
            session()->flash('error', 'Стол не найден');
            return;
        }
        
        // Проверка границ
        if (!$this->checkBounds($gridX, $gridY, $table['grid_width'], $table['grid_height'])) {
            session()->flash('error', 'Стол не помещается в эту позицию');
            return;
        }
        
        // Проверка коллизий
        if ($this->checkCollision(null, $gridX, $gridY, $table['grid_width'], $table['grid_height'], $table['rotation'])) {
            session()->flash('error', 'Место занято другим столом');
            return;
        }
        
        // ✅ КЛЮЧЕВОЕ ИЗМЕНЕНИЕ: Определяем зону ОБЯЗАТЕЛЬНО
        $zoneId = $this->detectZone($gridX, $gridY, $table['grid_width'], $table['grid_height']);
        
        // ✅ Если зона не найдена - ЗАПРЕЩАЕМ размещение!
        if (!$zoneId) {
            session()->flash('error', 'Невозможно разместить стол вне зоны. Сначала создайте зону в этой области.');
            return;
        }
        
        // Сохраняем в БД
        Resource::where('id', $this->selectedTableId)->update([
            'grid_x' => $gridX,
            'grid_y' => $gridY,
            'zone_id' => $zoneId, // Всегда с зоной
        ]);
        
        $this->loadTables();
        $this->selectedTableId = null;
        
        session()->flash('success', "Стол размещен на позиции ({$gridX}, {$gridY})");
    }

    /**
     * Переместить стол (drag & drop)
     */
    public function moveTable($tableId, $gridX, $gridY)
    {
        $table = collect($this->tablesOnGrid)->firstWhere('id', $tableId);
        
        if (!$table) {
            session()->flash('error', 'Стол не найден');
            $this->loadTables();
            return;
        }
        
        // Проверка границ
        if (!$this->checkBounds($gridX, $gridY, $table['grid_width'], $table['grid_height'], $table['rotation'])) {
            session()->flash('error', 'Стол выходит за границы');
            $this->loadTables();
            return;
        }
        
        // Проверка коллизий (исключая сам стол)
        if ($this->checkCollision($tableId, $gridX, $gridY, $table['grid_width'], $table['grid_height'], $table['rotation'])) {
            session()->flash('error', 'Место занято');
            $this->loadTables();
            return;
        }
        
        // ✅ Определяем зону ОБЯЗАТЕЛЬНО
        $zoneId = $this->detectZone($gridX, $gridY, $table['grid_width'], $table['grid_height'], $table['rotation']);
        
        // ✅ Если зона не найдена - ЗАПРЕЩАЕМ перемещение!
        if (!$zoneId) {
            session()->flash('error', 'Невозможно переместить стол вне зоны');
            $this->loadTables();
            return;
        }
        
        // Обновляем с новой зоной
        Resource::where('id', $tableId)->update([
            'grid_x' => $gridX,
            'grid_y' => $gridY,
            'zone_id' => $zoneId, // ✅ Всегда обновляем зону!
        ]);
        
        $this->loadTables();
        
        // Получаем название новой зоны для сообщения
        $newZone = collect($this->zones)->firstWhere('id', $zoneId);
        $zoneName = $newZone ? $newZone['name'] : 'неизвестная';
        
        session()->flash('success', "Стол перемещен в зону \"{$zoneName}\" на позицию ({$gridX}, {$gridY})");
    }

    /**
     * Повернуть стол на 90°
     */
    public function rotateTable($tableId)
    {
        $table = collect($this->tablesOnGrid)->firstWhere('id', $tableId);
        
        if (!$table) {
            session()->flash('error', 'Стол не найден');
            return;
        }
        
        $resource = Resource::find($tableId);
        $newRotation = ($resource->rotation + 90) % 360;
        
        // При повороте меняем ширину и высоту местами
        $newWidth = $table['grid_height'];
        $newHeight = $table['grid_width'];
        
        // Проверяем, что после поворота стол помещается
        if (!$this->checkBounds($table['grid_x'], $table['grid_y'], $newWidth, $newHeight, $newRotation)) {
            session()->flash('error', 'После поворота стол выходит за границы');
            return;
        }
        
        // Проверяем коллизии с новыми размерами
        if ($this->checkCollision($tableId, $table['grid_x'], $table['grid_y'], $newWidth, $newHeight, $newRotation)) {
            session()->flash('error', 'После поворота стол пересекается с другим столом');
            return;
        }
        
        // ✅ Проверяем, что после поворота стол все еще в зоне
        $zoneId = $this->detectZone($table['grid_x'], $table['grid_y'], $newWidth, $newHeight, $newRotation);
        
        if (!$zoneId) {
            session()->flash('error', 'После поворота стол выходит за пределы зоны');
            return;
        }
        
        $resource->update([
            'rotation' => $newRotation,
            'grid_width' => $newWidth,
            'grid_height' => $newHeight,
            'zone_id' => $zoneId, // ✅ Обновляем зону на всякий случай
        ]);
        
        $this->loadTables();
        session()->flash('success', 'Стол повернут на 90°');
    }

    /**
     * Убрать стол с карты
     */
    public function removeTable($tableId)
    {
        // ✅ КЛЮЧЕВОЕ ИЗМЕНЕНИЕ: Обнуляем ВСЕ связи!
        Resource::where('id', $tableId)->update([
            'grid_x' => null,
            'grid_y' => null,
            'zone_id' => null,  // ✅ Обнуляем зону!
            // place_id НЕ трогаем - стол все еще принадлежит заведению
        ]);
        
        $this->loadTables();
        $this->selectedGridTableId = null;
        session()->flash('success', 'Стол убран с карты и отвязан от зоны');
    }

    /**
     * Выбрать стол на карте
     */
    public function selectGridTable($tableId)
    {
        $this->selectedGridTableId = $this->selectedGridTableId === $tableId ? null : $tableId;
        $this->selectedTableId = null;
    }

    /**
     * Проверка границ
     */
    private function checkBounds($x, $y, $width, $height, $rotation = 0)
    {
        // При повороте 90° или 270° меняем местами ширину и высоту
        if ($rotation === 90 || $rotation === 270) {
            [$width, $height] = [$height, $width];
        }
        
        return $x >= 0 && $y >= 0 && 
               $x + $width <= $this->gridWidth && 
               $y + $height <= $this->gridHeight;
    }

    /**
     * Проверка коллизий
     */
    private function checkCollision($excludeId, $x, $y, $width, $height, $rotation = 0)
    {
        // При повороте меняем размеры
        if ($rotation === 90 || $rotation === 270) {
            [$width, $height] = [$height, $width];
        }
        
        foreach ($this->tablesOnGrid as $table) {
            if ($table['id'] == $excludeId) continue;
            
            $tableWidth = $table['grid_width'];
            $tableHeight = $table['grid_height'];
            
            // Учитываем поворот существующего стола
            if ($table['rotation'] === 90 || $table['rotation'] === 270) {
                [$tableWidth, $tableHeight] = [$tableHeight, $tableWidth];
            }
            
            // Проверка пересечения прямоугольников
            if (!($x + $width <= $table['grid_x'] || 
                  $x >= $table['grid_x'] + $tableWidth || 
                  $y + $height <= $table['grid_y'] || 
                  $y >= $table['grid_y'] + $tableHeight)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * ✅ УЛУЧШЕННЫЙ метод определения зоны
     * Теперь проверяет ВСЕ ячейки стола, а не только центр
     */
    private function detectZone($x, $y, $width, $height, $rotation = 0)
    {
        if (empty($this->zones)) {
            return null;
        }
        
        // При повороте меняем размеры
        if ($rotation === 90 || $rotation === 270) {
            [$width, $height] = [$height, $width];
        }
        
        // Собираем все ячейки, которые занимает стол
        $tableCells = [];
        for ($cellY = $y; $cellY < $y + $height; $cellY++) {
            for ($cellX = $x; $cellX < $x + $width; $cellX++) {
                $tableCells[] = ['x' => $cellX, 'y' => $cellY];
            }
        }
        
        // Подсчитываем сколько ячеек стола попадает в каждую зону
        $zoneMatches = [];
        
        foreach ($this->zones as $zone) {
            if (empty($zone['coordinates'])) continue;
            
            $matchCount = 0;
            foreach ($tableCells as $cell) {
                // Проверяем есть ли эта ячейка в координатах зоны
                foreach ($zone['coordinates'] as $coord) {
                    if ($coord['x'] == $cell['x'] && $coord['y'] == $cell['y']) {
                        $matchCount++;
                        break;
                    }
                }
            }
            
            if ($matchCount > 0) {
                $zoneMatches[$zone['id']] = $matchCount;
            }
        }
        
        // ✅ ВАЖНО: Если хотя бы одна ячейка стола вне всех зон - возвращаем null
        $totalCells = count($tableCells);
        $totalMatched = array_sum($zoneMatches);
        
        if ($totalMatched < $totalCells) {
            // Не все ячейки стола в зонах
            return null;
        }
        
        // Если все ячейки в зонах, возвращаем зону с наибольшим покрытием
        if (!empty($zoneMatches)) {
            arsort($zoneMatches);
            return array_key_first($zoneMatches);
        }
        
        return null;
    }

    public function render()
    {
        return view('livewire.admin.table-editor')
            ->layout('admin.layout.app-livewire')
            ->title('Редактор столов');
    }
}