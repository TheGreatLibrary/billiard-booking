<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Resource;
use App\Services\ResourceService;

class ResourceManager extends Component
{
    use WithPagination;

    public $model_id;
    public $zone_id;
    public $state_id;
    public $code;
    public $note;
    public $editingResourceId = null;

    protected $paginationTheme = 'tailwind';

    protected $rules = [
        'model_id' => 'required|exists:product_models,id',
        'zone_id' => 'required|exists:zones,id',
        'state_id' => 'required|exists:state_product,id',
        'code' => 'nullable|string|max:50',
        'note' => 'nullable|string',
    ];

    public function render(ResourceService $service)
    {
        $resources = $service->getAllResourcesPaginated();
        $formData = $service->getFormData();

        return view('livewire.admin.resource-manager', [
            'resources' => $resources,
            'models' => $formData['models'],
            'zones' => $formData['zones'],
            'states' => $formData['states'],
        ])->layout('layouts.app-livewire');
    }

    public function resetForm()
    {
        $this->reset(['model_id', 'zone_id', 'state_id', 'code', 'note', 'editingResourceId']);
    }

    public function edit($id)
    {
        $resource = Resource::findOrFail($id);
        $this->editingResourceId = $id;
        $this->model_id = $resource->model_id;
        $this->zone_id = $resource->zone_id;
        $this->state_id = $resource->state_id;
        $this->code = $resource->code;
        $this->note = $resource->note;
    }

    public function save(ResourceService $service)
    {
        $data = $this->validate();

        if ($this->editingResourceId) {
            $service->updateResource(Resource::findOrFail($this->editingResourceId), $data);
            session()->flash('success', 'Ресурс обновлён!');
        } else {
            $service->createResource($data);
            session()->flash('success', 'Ресурс добавлен!');
        }

        $this->resetForm();
    }

    public function delete(ResourceService $service, $id)
    {
        $resource = Resource::findOrFail($id);
        $service->deleteResource($resource);

        session()->flash('success', 'Ресурс удалён!');
    }
}
