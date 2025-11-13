<?php

namespace App\Livewire\Admin;

use App\Models\Place;
use App\Services\PlaceService;
use Livewire\Component;

class PlaceFormEdit extends Component
{
    public Place $place;
    
    public $name;
    public $address;
    public $description;

    protected $placeService;

    public function boot(PlaceService $placeService)
    {
        $this->placeService = $placeService;
    }

    public function mount(Place $place)
    {
        $this->place = $place;
        $this->name = $place->name;
        $this->address = $place->address;
        $this->description = $place->description;
    }

    public function rules()
    {
        return $this->placeService->getValidationRules();
    }

    public function save()
    {
        $validated = $this->validate();
        
        $this->placeService->update($this->place, $validated);

        session()->flash('success', 'Локация обновлена.');
        
        return redirect()->route('admin.places.list');
    }

    public function render()
    {
    
        return view('livewire.admin.place-form-edit')->layout('admin.layout.app-livewire');
    }
}