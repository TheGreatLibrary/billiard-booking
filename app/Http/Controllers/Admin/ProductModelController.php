<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductModel;
use App\Models\ProductType;
use Illuminate\Http\Request;

class ProductModelController extends Controller
{
    public function index()
    {
        // Выводим модель + тип для таблицы
        $models = ProductModel::with('type')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.product_models.index', compact('models'));
    }

    public function create()
    {
        $types = ProductType::orderBy('name')->get();
        return view('admin.product_models.create', compact('types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_type_id' => 'required|exists:product_types,id',
            'name' => 'required|string|max:255',
            'base_price_hour' => 'nullable|integer|min:0',
            'base_price_each' => 'nullable|integer|min:0',
        ]);

        ProductModel::create($validated);

        return redirect()->route('admin.product-models.index')
            ->with('success', 'Модель продукта успешно создана.');
    }

    public function edit(ProductModel $productModel)
    {
        $types = ProductType::orderBy('name')->get();
        return view('admin.product_models.edit', compact('productModel', 'types'));
    }

    public function update(Request $request, ProductModel $productModel)
    {
        $validated = $request->validate([
            'product_type_id' => 'required|exists:product_types,id',
            'name' => 'required|string|max:255',
            'base_price_hour' => 'nullable|integer|min:0',
            'base_price_each' => 'nullable|integer|min:0',
        ]);

        $productModel->update($validated);

        return redirect()->route('admin.product-models.index')
            ->with('success', 'Модель продукта обновлена.');
    }

    public function destroy(ProductModel $productModel)
    {
        $productModel->delete();

        return redirect()->route('admin.product-models.index')
            ->with('success', 'Модель продукта удалена.');
    }
}
