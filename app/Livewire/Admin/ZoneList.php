<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Zone;
use App\Models\Place;

class ZoneList extends Component
{
    use WithPagination;

    public $search = '';
    public $place_id = '';
    public $places = [];

    protected $queryString = ['search', 'place_id'];

    public function updating($field)
    {
        if (in_array($field, ['search', 'place_id'])) {
            $this->resetPage();
        }
    }

    public function mount()
    {
        $this->places = Place::all();
    }

    public function delete($id)
    {
        Zone::findOrFail($id)->delete();
        session()->flash('success', 'Зона успешно удалена.');
    }

    public function render()
    {
        $zones = Zone::with('place')
            ->when($this->place_id, fn($q) => $q->where('place_id', $this->place_id))
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->paginate(10);

        return view('livewire.admin.zone-list', compact('zones'))
            ->layout('admin.layout.app-livewire', ['title' => 'Зоны']);
    }
}
