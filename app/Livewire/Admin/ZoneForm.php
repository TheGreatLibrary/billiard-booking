<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Zone;
use App\Models\Place;

class ZoneForm extends Component
{
    public $zoneId;
    public $place_id;
    public $name;
    public $price_coef;
    public $places;

    protected $rules = [
        'place_id' => 'required|exists:places,id',
        'name' => 'required|string|max:255',
        'price_coef' => 'required|numeric|between:0,9999.999',
    ];

    public function mount($zone = null)
    {
        $this->places = Place::all();

        if ($zone) {
            $zone = Zone::findOrFail($zone);
            $this->zoneId = $zone->id;
            $this->place_id = $zone->place_id;
            $this->name = $zone->name;
            $this->price_coef = $zone->price_coef;
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'place_id' => $this->place_id,
            'name' => $this->name,
            'price_coef' => $this->price_coef,
        ];

        if ($this->zoneId) {
            Zone::findOrFail($this->zoneId)->update($data);
            session()->flash('success', 'Зона обновлена.');
        } else {
            Zone::create($data);
            session()->flash('success', 'Зона создана.');
        }

        return redirect()->route('admin.zones.index');
    }

    public function render()
    {
        return view('livewire.admin.zone-form')
            ->layout('admin.layout.app-livewire', [
                'title' => $this->zoneId ? 'Редактирование зоны' : 'Создание зоны'
            ]);
    }
}
