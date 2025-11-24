<?php

namespace App\Livewire\Admin;

use App\Services\PlaceService;
use Livewire\Component;

class PlaceFormCreate extends Component
{
    public $name = '';
    public $address = '';
    public $description = '';

    protected $placeService;

    public function boot(PlaceService $placeService)
    {
        $this->placeService = $placeService;
    }

    public function rules()
    {
        return $this->placeService->getValidationRules();
    }

    public function save()
    {
        $validated = $this->validate();
        
        $this->placeService->create($validated);

        session()->flash('success', 'Локация успешно создана.');
        
        return redirect()->route('admin.places.index');
    }

    public function render()
    {
        return view('livewire.admin.place-form-create')->layout('admin.layout.app-livewire');
    }
}