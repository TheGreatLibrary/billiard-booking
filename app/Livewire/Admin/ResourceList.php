<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Resource;
use App\Models\ProductModel;
use App\Models\Zone;

class ResourceList extends Component
{
    use WithPagination;

    public $search = '';
    public $model_id = '';
    public $zone_id = '';
    public $type = ''; // Фильтр по типу
    
    public $models = [];
    public $zones = [];

    protected $queryString = ['search', 'model_id', 'zone_id', 'type'];

    protected $paginationTheme = 'tailwind';

    public function updating($field)
    {
        if (in_array($field, ['search', 'model_id', 'zone_id', 'type'])) {
            $this->resetPage();
        }
    }

    public function mount()
    {
        $this->models = ProductModel::all();
        $this->zones = Zone::all();
    }

    public function delete($id)
    {
        Resource::findOrFail($id)->delete();
        session()->flash('success', 'Ресурс успешно удалён.');
    }

    public function render()
    {
        $resources = Resource::with(['productModel', 'zone', 'state', 'place'])
            ->when($this->search, fn($q) =>
                $q->where('code', 'like', "%{$this->search}%")
                  ->orWhereHas('productModel', fn($m) => $m->where('name', 'like', "%{$this->search}%"))
            )
            ->when($this->model_id, fn($q) => $q->where('model_id', $this->model_id))
            ->when($this->zone_id, fn($q) => $q->where('zone_id', $this->zone_id))
            ->when($this->type, fn($q) => $q->where('type', $this->type))
            ->orderBy('type', 'asc')
            ->orderBy('id', 'asc')
            ->paginate(15);

        return view('livewire.admin.resource-list', compact('resources'))
            ->layout('admin.layout.app-livewire', ['title' => 'Ресурсы']);
    }
}