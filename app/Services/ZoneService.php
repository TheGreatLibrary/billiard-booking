<?php

namespace App\Services;

use App\Models\Zone;

class ZoneService
{
    public function getAllPaginated($perPage = 15)
    {
        return Zone::with('place')->paginate($perPage);
    }

    public function create(array $data): Zone
    {
        return Zone::create($data);
    }

    public function update(Zone $zone, array $data): Zone
    {
        $zone->update($data);
        return $zone;
    }

    public function delete(Zone $zone): void
    {
        $zone->delete();
    }
}
