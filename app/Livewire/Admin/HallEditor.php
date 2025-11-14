<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\{Place, Zone, Resource, ProductModel, StateProduct};

class HallEditor extends Component
{
    // Выбранное место
    public $placeId = null;
    public $place = null;
    
    // Режим редактирования
    public $mode = 'tables'; // 'tables' или 'zones'
    
    // Данные
    public $resources = [];
    public $zones = [];
    public $places = [];
    
    // Параметры сетки
    public $gridWidth = 20;
    public $gridHeight = 10;
    public $cellSize = 60;
    
    // Выбранные элементы
    public $selectedResourceId = null;
    public $selectedTableId = null;
    
    // Для зон
    public $drawingZone = false;
    public $zonePoints = [];
    public $editingZoneId = null;

    public function mount()
    {
        $this->places = Place::all();
        
        // Если есть только одно место - выбрать его автоматически
        if ($this->places->count() === 1) {
            $this->placeId = $this->places->first()->id;
            $this->updatedPlaceId($this->placeId);
        }
    }

    public function updatedPlaceId($value)
    {
        if (!$value) {
            $this->place = null;
            $this->resources = [];
            $this->zones = [];
            return;
        }
        
        $this->place = Place::findOrFail($value);
        $this->gridWidth = $this->place->grid_width ?? 20;
        $this->gridHeight = $this->place->grid_height ?? 10;
        
        $this->loadResources();
        $this->loadZones();
    }

    private function loadResources()
    {
        if (!$this->place) return;
        
        $this->resources = Resource::whereHas('zone', function($q) {
                $q->where('place_id', $this->place->id);
            })
            ->with(['model', 'zone', 'state'])
            ->get()
            ->map(fn($r) => [
                'id' => $r->id,
                'code' => $r->code ?? 'N/A',
                'model_name' => $r->model->name ?? 'Unknown',
                'zone_id' => $r->zone_id,
                'zone_name' => $r->zone->name ?? 'N/A',
                'state' => $r->state->name ?? 'unknown',
                'grid_x' => $r->grid_x,
                'grid_y' => $r->grid_y,
                'grid_width' => $r->grid_width ?? 2,
                'grid_height' => $r->grid_height ?? 1,
                'rotation' => $r->rotation ?? 0,
                'on_grid' => !is_null($r->grid_x) && !is_null($r->grid_y),
            ])
            ->toArray();
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
                'price_coef' => $z->price_coef,
                'coordinates' => is_string($z->coordinates) ? json_decode($z->coordinates, true) : ($z->coordinates ?? []),
            ])
            ->toArray();
    }

    /**
     * Выбрать стол из контейнера
     */
    public function selectTableFromContainer($resourceId)
    {
        $this->selectedTableId = $resourceId;
        $this->dispatch('table-selected', resourceId: $resourceId);
    }

    /**
     * Разместить стол по клику на сетке
     */
    public function placeTableOnGrid($resourceId, $gridX, $gridY)
    {
        $resource = collect($this->resources)->firstWhere('id', $resourceId);
        
        if (!$resource) {
            session()->flash('error', 'Стол не найден');
            return;
        }
        
        // Проверка границ
        if ($gridX < 0 || $gridY < 0 || 
            $gridX + $resource['grid_width'] > $this->gridWidth || 
            $gridY + $resource['grid_height'] > $this->gridHeight) {
            session()->flash('error', 'Стол не помещается в эту позицию');
            return;
        }
        
        // Проверка коллизий
        if ($this->checkCollision($resourceId, $gridX, $gridY, $resource['grid_width'], $resource['grid_height'])) {
            session()->flash('error', 'Место занято другим столом');
            return;
        }
        
        // Сохраняем в БД
        Resource::where('id', $resourceId)->update([
            'grid_x' => $gridX,
            'grid_y' => $gridY,
        ]);
        
        $this->loadResources();
        $this->selectedTableId = null;
        session()->flash('success', 'Стол размещен на позиции (' . $gridX . ', ' . $gridY . ')');
    }

    /**
     * Обновить позицию стола (drag & drop)
     */
    public function updateTablePosition($resourceId, $gridX, $gridY)
    {
        $resource = collect($this->resources)->firstWhere('id', $resourceId);
        
        if (!$resource) {
            $this->loadResources();
            return;
        }
        
        // Проверка границ
        if ($gridX < 0 || $gridY < 0 || 
            $gridX + $resource['grid_width'] > $this->gridWidth || 
            $gridY + $resource['grid_height'] > $this->gridHeight) {
            session()->flash('error', 'Стол выходит за границы');
            $this->loadResources();
            return;
        }
        
        // Проверка коллизий (исключая сам стол)
        if ($this->checkCollision($resourceId, $gridX, $gridY, $resource['grid_width'], $resource['grid_height'])) {
            session()->flash('error', 'Место занято');
            $this->loadResources();
            return;
        }
        
        Resource::where('id', $resourceId)->update([
            'grid_x' => $gridX,
            'grid_y' => $gridY,
        ]);
        
        $this->loadResources();
        session()->flash('success', 'Позиция обновлена: (' . $gridX . ', ' . $gridY . ')');
    }

    /**
     * Проверка коллизий
     */
    private function checkCollision($resourceId, $x, $y, $width, $height)
    {
        foreach ($this->resources as $r) {
            if ($r['id'] == $resourceId || !$r['on_grid']) continue;
            
            // Проверка пересечения прямоугольников
            if (!($x + $width <= $r['grid_x'] || 
                  $x >= $r['grid_x'] + $r['grid_width'] || 
                  $y + $height <= $r['grid_y'] || 
                  $y >= $r['grid_y'] + $r['grid_height'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Повернуть стол
     */
    public function rotateTable($resourceId)
    {
        $resource = Resource::find($resourceId);
        if (!$resource) {
            session()->flash('error', 'Стол не найден');
            return;
        }
        
        $newRotation = ($resource->rotation + 90) % 360;
        $resource->update(['rotation' => $newRotation]);
        
        $this->loadResources();
        session()->flash('success', 'Стол повернут на 90°');
    }

    /**
     * Убрать стол с сетки
     */
    public function removeTableFromGrid($resourceId)
    {
        Resource::where('id', $resourceId)->update([
            'grid_x' => null,
            'grid_y' => null,
        ]);
        
        $this->loadResources();
        session()->flash('success', 'Стол убран с карты');
    }

    /**
     * Обновить размеры сетки
     */
    public function updateGridSize()
    {
        $this->validate([
            'gridWidth' => 'required|integer|min:5|max:50',
            'gridHeight' => 'required|integer|min:5|max:50',
        ]);
        
        if (!$this->place) return;
        
        $this->place->update([
            'grid_width' => $this->gridWidth,
            'grid_height' => $this->gridHeight,
        ]);
        
        session()->flash('success', 'Размеры сетки обновлены');
    }

    // ==================== МЕТОДЫ ДЛЯ ЗОН ====================

    /**
     * Начать рисование новой зоны
     */
    public function startDrawingZone()
    {
        $this->drawingZone = true;
        $this->zonePoints = [];
        $this->editingZoneId = null;
    }

    /**
     * Добавить точку к зоне
     */
    public function addZonePoint($x, $y)
    {
        if (!$this->drawingZone) return;
        
        $this->zonePoints[] = ['x' => $x, 'y' => $y];
        $this->dispatch('zone-point-added', point: ['x' => $x, 'y' => $y]);
    }

    /**
     * Завершить рисование зоны
     */
    public function finishZone($zoneName, $color, $priceCoef)
    {
        if (count($this->zonePoints) < 3) {
            session()->flash('error', 'Зона должна содержать минимум 3 точки');
            return;
        }

        Zone::create([
            'place_id' => $this->place->id,
            'name' => $zoneName,
            'color' => $color,
            'price_coef' => $priceCoef,
            'coordinates' => json_encode($this->zonePoints),
        ]);

        $this->drawingZone = false;
        $this->zonePoints = [];
        $this->loadZones();
        
        session()->flash('success', 'Зона "' . $zoneName . '" создана');
    }

    /**
     * Отменить рисование зоны
     */
    public function cancelZoneDrawing()
    {
        $this->drawingZone = false;
        $this->zonePoints = [];
        $this->dispatch('zone-drawing-cancelled');
    }

    /**
     * Удалить зону
     */
    public function deleteZone($zoneId)
    {
        Zone::find($zoneId)?->delete();
        $this->loadZones();
        session()->flash('success', 'Зона удалена');
    }

    public function render()
    {
        return view('livewire.admin.hall-editor')
            ->layout('admin.layout.app-livewire')
            ->title('Редактор зала');
    }
}