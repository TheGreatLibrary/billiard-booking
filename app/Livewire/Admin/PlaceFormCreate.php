<?php

namespace App\Livewire\Admin;

use App\Services\PlaceService;
use Livewire\Component;
use Livewire\WithFileUploads;

class PlaceFormCreate extends Component
{
    use WithFileUploads;

    public $name = '';
    public $address = '';
    public $description = '';
    public $hall_image;

    protected $placeService;

    public function boot(PlaceService $placeService)
    {
        $this->placeService = $placeService;
    }

    public function rules()
    {
        return array_merge($this->placeService->getValidationRules(), [
            'hall_image' => 'nullable|image|max:5120', // до 5MB
        ]);
    }

    public function save()
    {
        $validated = $this->validate();

        if ($this->hall_image) {
            $validated['hall_image'] = $this->hall_image->store('halls', 'public');
        }

        $this->placeService->create($validated);
        session()->flash('success', 'Локация успешно создана.');
        return redirect()->route('admin.places.index');
    }

    public function render()
    {
        return view('livewire.admin.place-form-create')->layout('admin.layout.app-livewire');
    }
}