<?php

namespace App\Services;

use App\Models\Place;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PlaceService
{
    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return Place::orderBy('name')->paginate($perPage);
    }

    public function create(array $data): Place
    {
        return Place::create($data);
    }

    public function update(Place $place, array $data): bool
    {
        return $place->update($data);
    }

    public function delete(Place $place): bool
    {
        return $place->delete();
    }

    public function find(int $id): ?Place
    {
        return Place::find($id);
    }

    public function getValidationRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'description' => 'nullable|string',
        ];
    }
}