<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductType;
use Illuminate\Http\Request;

class ProductTypeController extends Controller
{
    public function index()
    {
        $types = ProductType::orderBy('name')->paginate(15);
        return view('admin.product_types.index', compact('types'));
    }

    public function create()
    {
        return view('admin.product_types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:product_types,name',
        ]);

        ProductType::create($validated);

        return redirect()->route('admin.product-types.index')
            ->with('success', 'Тип продукта успешно создан.');
    }

    public function edit(ProductType $productType)
    {
        return view('admin.product_types.edit', compact('productType'));
    }

    public function update(Request $request, ProductType $productType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:product_types,name,' . $productType->id,
        ]);

        $productType->update($validated);

        return redirect()->route('admin.product-types.index')
            ->with('success', 'Тип продукта обновлён.');
    }

    public function destroy(ProductType $productType)
    {
        $productType->delete();

        return redirect()->route('admin.product-types.index')
            ->with('success', 'Тип продукта удалён.');
    }
}
