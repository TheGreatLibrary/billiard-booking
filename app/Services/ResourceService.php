<?php

namespace App\Services;

use App\Models\Resource;
use App\Models\ProductModel;
use App\Models\Zone;
use App\Models\StateProduct;
use Illuminate\Support\Collection;

class ResourceService
{
    public function getAllResourcesPaginated(int $perPage = 20)
    {
        return Resource::with(['model', 'zone', 'state'])->paginate($perPage);
    }

    public function getFormData(): array
    {
        return [
            'models' => ProductModel::all(),
            'zones' => Zone::all(),
            'states' => StateProduct::all(),
        ];
    }

    public function createResource(array $data): Resource
    {
        return Resource::create($data);
    }

    public function updateResource(Resource $resource, array $data): Resource
    {
        $resource->update($data);
        return $resource;
    }

    public function deleteResource(Resource $resource): void
    {
        $resource->delete();
    }
}
