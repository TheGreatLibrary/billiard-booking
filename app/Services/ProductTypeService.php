<?php

namespace App\Services;

use App\Models\ProductType;
use Illuminate\Validation\Rule;

class ProductTypeService
{
    public function validate(array $data, ?ProductType $type = null): array
    {
        return validator($data, [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('product_types', 'name')->ignore($type?->id),
            ],
        ])->validate();
    }

    public function create(array $data): ProductType
    {
        return ProductType::create($data);
    }

    public function update(ProductType $type, array $data): bool
    {
        return $type->update($data);
    }

    public function delete(ProductType $type): bool
    {
        return $type->delete();
    }

    public function deleteById($id): bool
    {
        $type = ProductType::findOrFail($id);
        return $type->delete();
    }

    public function getAll()
    {
        return ProductType::latest()->paginate(10);
    }

public function search($term)
{
    return ProductType::query()
        ->when($term, fn($q) => $q->where('name', 'like', "%{$term}%"))
        ->latest()
        ->paginate(10);
}
}
