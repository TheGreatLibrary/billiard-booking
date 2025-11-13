<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ProductModel;
use App\Models\ProductType;

class ProductModelList extends Component
{
    use WithPagination;

    public $search = '';
    public $product_type_id = '';
    public $types = [];

    protected $queryString = ['search', 'product_type_id'];

    public function updating($field)
    {
        if (in_array($field, ['search', 'product_type_id'])) {
            $this->resetPage();
        }
    }

    public function mount()
    {
        $this->types = ProductType::orderBy('name')->get();
    }

    public function delete($id)
    {
        ProductModel::findOrFail($id)->delete();
        session()->flash('success', 'Модель продукта успешно удалена.');
    }

    public function render()
    {
        $models = ProductModel::with('type')
            ->when($this->product_type_id, fn($q) => $q->where('product_type_id', $this->product_type_id))
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.admin.product-model-list', compact('models'))
            ->layout('admin.layout.app-livewire', ['title' => 'Модели продуктов']);
    }
}