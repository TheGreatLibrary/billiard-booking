<?php

namespace App\Services;

use App\Models\ProductType;
use Illuminate\Validation\ValidationException;

class ProductTypeService
{
    public function getAll()
    {
        return ProductType::orderBy('name')->paginate(15);
    }

    public function create(array $data): ProductType
    {
        return ProductType::create($data);
    }

    public function update(ProductType $productType, array $data): ProductType
    {
        $productType->update($data);
        return $productType;
    }

    public function delete(ProductType $productType): void
    {
        $productType->delete();
    }

    public function validate(array $data, ?ProductType $productType = null): array
    {
        $id = $productType?->id ?? 'NULL';

        return validator($data, [
            'name' => "required|string|max:255|unique:product_types,name,{$id}",
        ])->validate();
    }
}
