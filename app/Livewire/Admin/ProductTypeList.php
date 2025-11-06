<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\ProductTypeService;

class ProductTypeList extends Component
{
    use WithPagination;

    public $search = '';
    

    protected $queryString = ['search'];
    protected ProductTypeService $service;

    public function boot(ProductTypeService $service)
    {
        $this->service = $service;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        $this->service->deleteById($id);
        session()->flash('success', 'Тип продукта удалён.');
    }

public function render()
{
    $types = $this->service->search($this->search);

    return view('livewire.admin.product-type-list', compact('types'))
        ->layout('admin.layout.app-livewire', ['title' => 'Типы продуктов']);
}
}
