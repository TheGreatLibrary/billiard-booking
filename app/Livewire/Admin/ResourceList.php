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
    public $models = [];
    public $zones = [];

    protected $queryString = ['search', 'model_id', 'zone_id'];

    protected $paginationTheme = 'tailwind';

    public function updating($field)
    {
        if (in_array($field, ['search', 'model_id', 'zone_id'])) {
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
        $resources = Resource::with(['model', 'zone', 'state'])
            ->when($this->search, fn($q) =>
                $q->where('code', 'like', "%{$this->search}%")
                  ->orWhereHas('model', fn($m) => $m->where('name', 'like', "%{$this->search}%"))
            )
            ->when($this->model_id, fn($q) => $q->where('model_id', $this->model_id))
            ->when($this->zone_id, fn($q) => $q->where('zone_id', $this->zone_id))
            ->paginate(10);

        return view('livewire.admin.resource-list', compact('resources'))
            ->layout('admin.layout.app-livewire', ['title' => 'Ресурсы']);
    }
}
