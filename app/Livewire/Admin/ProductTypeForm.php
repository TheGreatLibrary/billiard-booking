<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\ProductType;
use App\Services\ProductTypeService;

class ProductTypeForm extends Component
{
    public $name = '';
    public $productTypeId = null;
    public $isEdit = false;

    protected ProductTypeService $service;

    public function boot(ProductTypeService $service)
    {
        $this->service = $service;
    }

    public function mount($id = null)
    {
        if ($id) {
            $type = ProductType::findOrFail($id);
            $this->productTypeId = $type->id;
            $this->name = $type->name;
            $this->isEdit = true;
        }
    }

    public function save()
    {
        $data = ['name' => $this->name];

        if ($this->isEdit) {
            $type = ProductType::findOrFail($this->productTypeId);
            $validated = $this->service->validate($data, $type);
            $this->service->update($type, $validated);
            session()->flash('success', 'Тип продукта обновлён.');
        } else {
            $validated = $this->service->validate($data);
            $this->service->create($validated);
            session()->flash('success', 'Тип продукта создан.');
        }

        return redirect()->route('admin.product-types.index');
    }

    public function render()
    {
        return view('livewire.admin.product-type-form')
            ->layout('admin.layout.app-livewire', [
                'title' => $this->isEdit ? 'Редактировать тип продукта' : 'Добавить тип продукта',
            ]);
    }
}
