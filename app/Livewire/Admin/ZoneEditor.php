<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\{Place, Zone};

class ZoneEditor extends Component
{
    public $placeId = null;
    public $place = null;
    
    // Данные
    public $places = [];
    public $zones = [];
    
    // Параметры сетки
    public $gridWidth = 20;
    public $gridHeight = 10;
    
    // Режим работы
    public $mode = 'select'; // 'select', 'draw', 'edit'
    
    // Текущая зона для редактирования/рисования
    public $editingZoneId = null;
    public $selectedCells = []; // [{x: 0, y: 0}, {x: 1, y: 0}, ...]
    
    // Форма зоны
    public $zoneName = '';
    public $zoneColor = '#3B82F6';
    public $zonePriceCoef = 1.0;
    
    // Выделенная зона (для просмотра)
    public $selectedZoneId = null;

    public function mount()
    {
        $this->places = Place::all();
        
        // Если только одно место - выбрать автоматически
        if ($this->places->count() === 1) {
            $this->placeId = $this->places->first()->id;
            $this->updatedPlaceId($this->placeId);
        }
    }

    public function updatedPlaceId($value)
    {
        if (!$value) {
            $this->place = null;
            $this->zones = [];
            $this->resetDrawing();
            return;
        }
        
        $this->place = Place::findOrFail($value);
        $this->gridWidth = $this->place->grid_width ?? 20;
        $this->gridHeight = $this->place->grid_height ?? 10;
        
        $this->loadZones();
        $this->resetDrawing();
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

    /**
     * Начать рисование новой зоны
     */
    public function startDrawing()
    {
        $this->mode = 'draw';
        $this->editingZoneId = null;
        $this->selectedCells = [];
        $this->zoneName = '';
        $this->zoneColor = '#3B82F6';
        $this->zonePriceCoef = 1.0;
        $this->selectedZoneId = null;
    }

    /**
     * Переключить выделение ячейки
     */
    public function toggleCell($x, $y)
    {
        if ($this->mode !== 'draw' && $this->mode !== 'edit') return;
        
        $cellKey = "{$x},{$y}";
        $index = array_search($cellKey, array_map(fn($c) => "{$c['x']},{$c['y']}", $this->selectedCells));
        
        if ($index !== false) {
            // Убрать ячейку
            array_splice($this->selectedCells, $index, 1);
        } else {
            // Добавить ячейку
            $this->selectedCells[] = ['x' => $x, 'y' => $y];
        }
    }

    /**
     * Выбрать зону для редактирования
     */
    public function editZone($zoneId)
    {
        $zone = collect($this->zones)->firstWhere('id', $zoneId);
        
        if (!$zone) {
            session()->flash('error', 'Зона не найдена');
            return;
        }
        
        $this->mode = 'edit';
        $this->editingZoneId = $zoneId;
        $this->selectedCells = $zone['coordinates'];
        $this->zoneName = $zone['name'];
        $this->zoneColor = $zone['color'];
        $this->zonePriceCoef = $zone['price_coef'];
        $this->selectedZoneId = null;
    }

    /**
     * Сохранить зону
     */
    public function saveZone()
    {
        $this->validate([
            'zoneName' => 'required|string|max:255',
            'zoneColor' => 'required|string|max:7',
            'zonePriceCoef' => 'required|numeric|between:0,9999.999',
        ]);

        if (empty($this->selectedCells)) {
            session()->flash('error', 'Выберите хотя бы одну ячейку для зоны');
            return;
        }

        $data = [
            'place_id' => $this->place->id,
            'name' => $this->zoneName,
            'color' => $this->zoneColor,
            'price_coef' => $this->zonePriceCoef,
            'coordinates' => $this->selectedCells,
        ];

        if ($this->editingZoneId) {
            // Обновление
            Zone::where('id', $this->editingZoneId)->update($data);
            session()->flash('success', "Зона \"{$this->zoneName}\" обновлена");
        } else {
            // Создание
            Zone::create($data);
            session()->flash('success', "Зона \"{$this->zoneName}\" создана");
        }

        $this->loadZones();
        $this->resetDrawing();
    }

    /**
     * Удалить зону
     */
    public function deleteZone($zoneId)
    {
        $zone = Zone::find($zoneId);
        
        if (!$zone) {
            session()->flash('error', 'Зона не найдена');
            return;
        }

        $zoneName = $zone->name;
        $zone->delete();
        
        $this->loadZones();
        $this->resetDrawing();
        
        session()->flash('success', "Зона \"{$zoneName}\" удалена");
    }

    /**
     * Отменить рисование/редактирование
     */
    public function cancelDrawing()
    {
        $this->resetDrawing();
    }

    /**
     * Сбросить состояние рисования
     */
    private function resetDrawing()
    {
        $this->mode = 'select';
        $this->editingZoneId = null;
        $this->selectedCells = [];
        $this->zoneName = '';
        $this->zoneColor = '#3B82F6';
        $this->zonePriceCoef = 1.0;
        $this->selectedZoneId = null;
    }

    /**
     * Выделить зону (для просмотра)
     */
    public function selectZone($zoneId)
    {
        if ($this->mode !== 'select') return;
        
        $this->selectedZoneId = $this->selectedZoneId === $zoneId ? null : $zoneId;
    }

    /**
     * Очистить выделение
     */
    public function clearSelection()
    {
        $this->selectedCells = [];
    }

    /**
     * Получить контрастный цвет для текста (белый или черный)
     */
    public function getContrastColor($hexColor)
    {
        // Убираем #
        $hexColor = ltrim($hexColor, '#');
        
        // Конвертируем в RGB
        $r = hexdec(substr($hexColor, 0, 2));
        $g = hexdec(substr($hexColor, 2, 2));
        $b = hexdec(substr($hexColor, 4, 2));
        
        // Вычисляем яркость
        $brightness = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
        
        // Возвращаем черный или белый в зависимости от яркости
        return $brightness > 155 ? '#000000' : '#FFFFFF';
    }

    public function render()
    {
        return view('livewire.admin.zone-editor')
            ->layout('admin.layout.app-livewire')
            ->title('Редактор зон');
    }
}