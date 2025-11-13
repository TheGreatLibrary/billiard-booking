<?php

namespace App\Services;

use App\Models\PriceRule;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PriceRuleService
{
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return PriceRule::with(['place', 'zone'])
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $data): PriceRule
    {
        return PriceRule::create($data);
    }

    public function update(PriceRule $rule, array $data): bool
    {
        return $rule->update($data);
    }

    public function delete(PriceRule $rule): bool
    {
        return $rule->delete();
    }

    public function getValidationRules(): array
    {
        return [
            'place_id'  => ['required', 'exists:places,id'],
            'zone_id'   => ['nullable', 'exists:zones,id'],
            'dow'       => ['nullable', 'integer', 'between:0,6'],
            'time_from' => ['nullable', 'date_format:H:i'],
            'time_to'   => ['nullable', 'date_format:H:i'],
            'kind'      => ['required', 'in:coef,override'],
            'value'     => ['required', 'numeric'],
            'active'    => ['boolean'],
        ];
    }

    public function prepareDataForSave(array $data): array
    {
        $data['active'] = (bool)($data['active'] ?? false);
        $data['zone_id'] = $data['zone_id'] ?: null;
        $data['dow'] = $data['dow'] !== '' ? $data['dow'] : null;
        $data['time_from'] = $data['time_from'] ?: null;
        $data['time_to'] = $data['time_to'] ?: null;
        
        return $data;
    }
}