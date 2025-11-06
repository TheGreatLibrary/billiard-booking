<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Resource;
use App\Models\ProductModel;
use App\Models\Zone;
use App\Models\StateProduct;

class ResourceForm extends Component
{
    public $resourceId;
    public $model_id;
    public $zone_id;
    public $state_id;
    public $code;
    public $note;

    public $models = [];
    public $zones = [];
    public $states = [];

    protected $rules = [
        'model_id' => 'required|exists:product_models,id',
        'zone_id' => 'required|exists:zones,id',
        'state_id' => 'required|exists:state_product,id',
        'code' => 'nullable|string|max:50',
        'note' => 'nullable|string|max:500',
    ];

    public function mount($resource = null)
    {
        $this->models = ProductModel::all();
        $this->zones = Zone::all();
        $this->states = StateProduct::all();

        if ($resource) {
            $r = Resource::findOrFail($resource);
            $this->resourceId = $r->id;
            $this->model_id = $r->model_id;
            $this->zone_id = $r->zone_id;
            $this->state_id = $r->state_id;
            $this->code = $r->code;
            $this->note = $r->note;
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
