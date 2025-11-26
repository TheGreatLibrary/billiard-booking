<?php

namespace App\Services;

use App\Models\ProductModel;
use App\Models\ProductType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProductModelService
{
    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return ProductModel::with('type')
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function getProductTypes(): Collection
    {
        return ProductType::orderBy('name')->get();
    }

    public function create(array $data): ProductModel
    {
        return ProductModel::create($data);
    }

    public function update(ProductModel $productModel, array $data): bool
    {
        return $productModel->update($data);
    }

    public function delete(ProductModel $productModel): bool
    {
        return $productModel->delete();
    }

    public function getValidationRules(): array
    {
        return [
            'product_type_id' => 'required|exists:product_types,id',
            'name' => 'required|string|max:255',
            'base_price_hour' => 'nullable|integer|min:0',
            'base_price_each' => 'nullable|integer|min:0',
        ];
    }
}