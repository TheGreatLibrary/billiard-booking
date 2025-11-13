<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\ProductModel;
use App\Models\ProductType;

class ProductModelForm extends Component
{
    public $modelId;
    public $product_type_id;
    public $name;
    public $base_price_hour;
    public $base_price_each;
    public $types;

    protected $rules = [
        'product_type_id' => 'required|exists:product_types,id',
        'name' => 'required|string|max:255',
        'base_price_hour' => 'nullable|integer|min:0',
        'base_price_each' => 'nullable|integer|min:0',
    ];

    public function mount($model = null)
    {
        $this->types = ProductType::orderBy('name')->get();

        if ($model) {
            $model = ProductModel::findOrFail($model);
            $this->modelId = $model->id;
            $this->product_type_id = $model->product_type_id;
            $this->name = $model->name;
            $this->base_price_hour = $model->base_price_hour;
            $this->base_price_each = $model->base_price_each;
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'product_type_id' => $this->product_type_id,
            'name' => $this->name,
            'base_price_hour' => $this->base_price_hour,
            'base_price_each' => $this->base_price_each,
        ];

        if ($this->modelId) {
            ProductModel::findOrFail($this->modelId)->update($data);
            session()->flash('success', 'Модель продукта обновлена.');
        } else {
            ProductModel::create($data);
            session()->flash('success', 'Модель продукта создана.');
        }

        return redirect()->route('admin.product-models.index');
    }

    public function render()
    {
        return view('livewire.admin.product-model-form')
            ->layout('admin.layout.app-livewire', [
                'title' => $this->modelId ? 'Редактирование модели продукта' : 'Создание модели продукта'
            ]);
    }
}