<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\{Place, Zone};

class ZoneEditor extends Component
{
    public $placeId = null;
    public $place = null;
    public $places = [];
    public $zones = [];
    public $gridWidth = 20;
    public $gridHeight = 10;
    
    public $editingZoneId = null;
    public $zoneName = '';
    public $zoneColor = '#3B82F6';
    public $zonePriceCoef = 1.0;

    public $colorPresets = [
        '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6',
        '#EC4899', '#06B6D4', '#84CC16', '#F97316', '#6366F1',
    ];

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
            $this->place = null;
            $this->zones = [];
            $this->resetForm();
            return;
        }
        $this->place = Place::findOrFail($value);
        $this->gridWidth = $this->place->grid_width ?? 20;
        $this->gridHeight = $this->place->grid_height ?? 10;
        $this->loadZones();
        $this->resetForm();

        $this->dispatch('zm:place-changed', [
            'gw' => $this->gridWidth,
            'gh' => $this->gridHeight,
            'zones' => $this->zones,
        ]);
    }

    private function loadZones()
    {
        if (!$this->place) return;
        $this->zones = Zone::where('place_id', $this->place->id)->get()->map(fn($z) => [
            'id' => $z->id,
            'name' => $z->name,
            'color' => $z->color ?? '#3B82F6',
            'price_coef' => (float) $z->price_coef,
            'coordinates' => is_string($z->coordinates) ? json_decode($z->coordinates, true) : ($z->coordinates ?? []),
        ])->toArray();
    }

    public function editZone($zoneId)
    {
        $zone = collect($this->zones)->firstWhere('id', $zoneId);
        if (!$zone) return;
        $this->editingZoneId = $zoneId;
        $this->zoneName = $zone['name'];
        $this->zoneColor = $zone['color'];
        $this->zonePriceCoef = $zone['price_coef'];

        $this->dispatch('zm:edit-zone', [
            'id' => $zone['id'],
            'cells' => $zone['coordinates'],
            'color' => $zone['color'],
        ]);
    }

    public function saveZoneWithCells($cells)
    {
        $this->validate([
            'zoneName' => 'required|string|max:255',
            'zoneColor' => 'required|string|max:7',
            'zonePriceCoef' => 'required|numeric|between:0,9999.999',
        ]);

        $normalized = collect($cells)->map(fn($c) => [
            'x' => (int)($c['x'] ?? 0), 'y' => (int)($c['y'] ?? 0),
        ])->unique(fn($c) => $c['x'].','.$c['y'])->values()->toArray();

        if (empty($normalized)) {
            session()->flash('error', 'Выделите ячейки на карте');
            return;
        }

        $data = [
            'place_id' => $this->place->id,
            'name' => $this->zoneName,
            'color' => $this->zoneColor,
            'price_coef' => $this->zonePriceCoef,
            'coordinates' => $normalized,
        ];

        if ($this->editingZoneId) {
            Zone::where('id', $this->editingZoneId)->update($data);
            session()->flash('success', "Зона \"{$this->zoneName}\" обновлена");
        } else {
            Zone::create($data);
            session()->flash('success', "Зона \"{$this->zoneName}\" создана");
        }

        $this->loadZones();
        $this->resetForm();
        $this->dispatch('zm:zones-updated', ['zones' => $this->zones]);
    }

    public function deleteZone($zoneId)
    {
        $zone = Zone::find($zoneId);
        if (!$zone) return;
        $name = $zone->name;
        $zone->delete();
        $this->loadZones();
        $this->resetForm();
        session()->flash('success', "Зона \"{$name}\" удалена");
        $this->dispatch('zm:zones-updated', ['zones' => $this->zones]);
    }

    public function resetForm()
    {
        $this->editingZoneId = null;
        $this->zoneName = '';
        $this->zoneColor = '#3B82F6';
        $this->zonePriceCoef = 1.0;
        $this->dispatch('zm:reset');
    }

    public function render()
    {
        return view('livewire.admin.zone-editor')
            ->layout('admin.layout.app-livewire')
            ->title('Редактор зон');
    }
}