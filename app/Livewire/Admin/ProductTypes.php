<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ProductType;
use App\Services\ProductTypeService;

class ProductTypes extends Component
{
    use WithPagination;

    public $name;
    public $productTypeId;
    public $isEdit = false;

    protected $service;

    public function boot(ProductTypeService $service)
    {
        $this->service = $service;
    }

    public function render()
    {
        return view('livewire.admin.product-types', [
            'types' => $this->service->getAll(),
        ])->layout('layouts.app-livewire'); // важно! новый layout
    }

    public function resetForm()
    {
        $this->reset(['name', 'productTypeId', 'isEdit']);
    }

    public function edit($id)
    {
        $type = ProductType::findOrFail($id);
        $this->name = $type->name;
        $this->productTypeId = $type->id;
        $this->isEdit = true;
    }

    public function save()
    {
        $data = ['name' => $this->name];

        if ($this->isEdit && $this->productTypeId) {
            $type = ProductType::findOrFail($this->productTypeId);
            $validated = $this->service->validate($data, $type);
            $this->service->update($type, $validated);
            session()->flash('success', 'Тип продукта обновлён.');
        } else {
            $validated = $this->service->validate($data);
            $this->service->create($validated);
            session()->flash('success', 'Тип продукта создан.');
        }

        $this->resetForm();
    }

    public function delete($id)
    {
        $type = ProductType::findOrFail($id);
        $this->service->delete($type);
        session()->flash('success', 'Тип продукта удалён.');
    }
}
