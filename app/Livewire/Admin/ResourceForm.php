<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Resource;
use App\Models\ProductModel;
use App\Models\Zone;
use App\Models\Place;
use App\Models\StateProduct;

class ResourceForm extends Component
{
    public $resourceId;
    public $model_id;
    public $place_id;
    public $zone_id;
    public $state_id;
    public $code;
    public $note;
    public $grid_x = 0;
    public $grid_y = 0;
    public $grid_width = 1;
    public $grid_height = 1;
    public $rotation = 0;

    public $models = [];
    public $places = [];
    public $zones = [];
    public $states = [];

    protected $rules = [
        'model_id' => 'required|exists:product_models,id',
        'place_id' => 'nullable|exists:places,id',
        'zone_id' => 'required|exists:zones,id',
        'state_id' => 'required|exists:state_product,id',
        'code' => 'nullable|string|max:50',
        'note' => 'nullable|string|max:500',
        'grid_x' => 'nullable|integer|min:0',
        'grid_y' => 'nullable|integer|min:0',
        'grid_width' => 'nullable|integer|min:1',
        'grid_height' => 'nullable|integer|min:1',
        'rotation' => 'nullable|integer|min:0|max:360',
    ];

    public function mount($resource = null)
    {
        $this->models = ProductModel::all();
        $this->places = Place::all();
        $this->zones = Zone::all();
        $this->states = StateProduct::all();

        if ($resource) {
            $r = Resource::findOrFail($resource);
            $this->resourceId = $r->id;
            $this->model_id = $r->model_id;
            $this->place_id = $r->place_id;
            $this->zone_id = $r->zone_id;
            $this->state_id = $r->state_id;
            $this->code = $r->code;
            $this->note = $r->note;
            $this->grid_x = $r->grid_x ?? 0;
            $this->grid_y = $r->grid_y ?? 0;
            $this->grid_width = $r->grid_width ?? 1;
            $this->grid_height = $r->grid_height ?? 1;
            $this->rotation = $r->rotation ?? 0;
        }
    }

    public function save()
    {
        $data = $this->validate();

        if ($this->resourceId) {
            Resource::findOrFail($this->resourceId)->update($data);
            session()->flash('success', 'Ресурс обновлён.');
        } else {
            Resource::create($data);
            session()->flash('success', 'Ресурс создан.');
        }

        return redirect()->route('admin.resources.index');
    }

    public function render()
    {
        return view('livewire.admin.resource-form')
            ->layout('admin.layout.app-livewire', [
                'title' => $this->resourceId ? 'Редактирование ресурса' : 'Создание ресурса'
            ]);
    }
}
