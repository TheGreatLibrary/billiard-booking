<?php

namespace App\Livewire\Admin;

use App\Models\Place;
use App\Services\PlaceService;
use Livewire\Component;
use Livewire\WithFileUploads;

class PlaceFormEdit extends Component
{
    use WithFileUploads;

    public Place $place;
    public $name;
    public $address;
    public $description;
    public $hall_image;
    public $existing_image;

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
        $this->existing_image = $place->hall_image;
    }

    public function rules()
    {
        return array_merge($this->placeService->getValidationRules(), [
            'hall_image' => 'nullable|image|max:5120',
        ]);
    }

    public function removeImage()
    {
        $this->existing_image = null;
        $this->hall_image = null;
    }

    public function save()
    {
        $validated = $this->validate();

        if ($this->hall_image) {
            // Удаляем старое изображение
            if ($this->place->hall_image) {
                \Storage::disk('public')->delete($this->place->hall_image);
            }
            $validated['hall_image'] = $this->hall_image->store('halls', 'public');
        } elseif ($this->existing_image === null && $this->place->hall_image) {
            // Пользователь удалил изображение
            \Storage::disk('public')->delete($this->place->hall_image);
            $validated['hall_image'] = null;
        }

        $this->placeService->update($this->place, $validated);
        session()->flash('success', 'Локация обновлена.');
        return redirect()->route('admin.places.index');
    }

    public function render()
    {
        return view('livewire.admin.place-form-edit')->layout('admin.layout.app-livewire');
    }
}