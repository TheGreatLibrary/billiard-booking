<?php

namespace App\Livewire\Admin;

use App\Services\PlaceService;
use Livewire\Component;
use Livewire\WithPagination;

class PlaceIndex extends Component
{
    use WithPagination;

    public $search = '';

    protected $placeService;

    public function boot(PlaceService $placeService)
    {
        $this->placeService = $placeService;
    }

    public function delete($id)
    {
        $place = $this->placeService->find($id);
        
        if ($place) {
            $this->placeService->delete($place);
            session()->flash('success', 'Локация удалена.');
        }
    }

    public function render()
    {
        
        return view('livewire.admin.place-index', [
            'places' => $this->placeService->getPaginated(15)
        ])->layout('admin.layout.app-livewire');
    }
}