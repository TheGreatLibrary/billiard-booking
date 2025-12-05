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
    public $grid_x = null;
    public $grid_y = null;
    public $grid_width = 1;
    public $grid_height = 1;
    public $rotation = 0;
    public $quantity = 1;
    public $type = 'table';

    public $models = [];
    public $places = [];
    public $zones = [];
    public $states = [];

    protected function rules()
    {
        return [
            'model_id' => 'required|exists:product_models,id',
            'place_id' => 'nullable|exists:places,id',
            'zone_id' => $this->type === 'table' ? 'required|exists:zones,id' : 'nullable|exists:zones,id',
            'state_id' => 'required|exists:state_product,id',
            'code' => 'nullable|string|max:50',
            'note' => 'nullable|string|max:500',
            'grid_x' => $this->type === 'table' ? 'required|integer|min:0' : 'nullable|integer|min:0',
            'grid_y' => $this->type === 'table' ? 'required|integer|min:0' : 'nullable|integer|min:0',
            'grid_width' => 'nullable|integer|min:1',
            'grid_height' => 'nullable|integer|min:1',
            'rotation' => 'nullable|integer|min:0|max:360',
            'quantity' => $this->type === 'equipment' ? 'required|integer|min:1' : 'required|integer|min:1|max:1',
            'type' => 'required|in:table,equipment',
        ];
    }

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
            $this->grid_x = $r->grid_x;
            $this->grid_y = $r->grid_y;
            $this->grid_width = $r->grid_width ?? 1;
            $this->grid_height = $r->grid_height ?? 1;
            $this->rotation = $r->rotation ?? 0;
            $this->quantity = $r->quantity ?? 1;
            $this->type = $r->type ?? 'table';
        }
    }

    public function updatedType()
    {
        // При смене типа на equipment - убираем координаты и zone_id
        if ($this->type === 'equipment') {
            $this->grid_x = null;
            $this->grid_y = null;
            $this->zone_id = null;
            $this->quantity = $this->quantity > 1 ? $this->quantity : 10;
        } else {
            // При смене на table - устанавливаем defaults
            $this->grid_x = $this->grid_x ?? 0;
            $this->grid_y = $this->grid_y ?? 0;
            $this->quantity = 1;
        }
    }

    public function save()
    {
        $data = $this->validate();

        // Для equipment убираем grid координаты если они null
        if ($this->type === 'equipment') {
            $data['grid_x'] = null;
            $data['grid_y'] = null;
            $data['zone_id'] = null;
        }

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