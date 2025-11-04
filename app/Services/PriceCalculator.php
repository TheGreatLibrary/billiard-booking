<?php

namespace App\Services;

use App\Models\PriceRule;
use App\Models\Resource;
use Carbon\Carbon;

class PriceCalculator
{
    /**
     * Рассчитать стоимость бронирования одного стола
     */
    public function calculateTablePrice(
        Resource $resource,
        Carbon $startsAt,
        Carbon $endsAt,
        int $placeId
    ): array {
        $minutes = $startsAt->diffInMinutes($endsAt);
        
        $priceRule = $this->findPriceRule(
            $placeId,
            $resource->zone_id,
            $startsAt
        );

        $basePrice = (int) $resource->model->base_price_hour;
        $zoneCoef = (float) $resource->zone->price_coef;
        
        $ruleKind = $priceRule?->kind ?? 'coef';
        $ruleValue = $priceRule?->value ?? 1.0;

        if ($ruleKind === 'coef') {
            $hourPrice = $basePrice * $zoneCoef * $ruleValue;
        } else {
            $hourPrice = $ruleValue;
        }

        $amount = (int) round(($hourPrice / 60) * $minutes);

        return [
            'amount' => $amount,
            'minutes' => $minutes,
            'hour_price_snapshot' => $basePrice,
            'zone_coef_snapshot' => $zoneCoef,
            'rule_kind' => $ruleKind,
            'rule_value' => $ruleValue,
        ];
    }

    /**
     * Найти подходящее ценовое правило
     */
    private function findPriceRule(int $placeId, int $zoneId, Carbon $datetime): ?PriceRule
    {
        $dow = $datetime->dayOfWeek;
        $time = $datetime->format('H:i');

        return PriceRule::where('place_id', $placeId)
            ->where('active', 1)
            ->where(fn($q) => $q->whereNull('zone_id')->orWhere('zone_id', $zoneId))
            ->where(fn($q) => $q->whereNull('dow')->orWhere('dow', $dow))
            ->where(fn($q) => $q->whereNull('time_from')
                ->orWhere(fn($qq) => $qq->where('time_from', '<=', $time)
                                       ->where('time_to', '>', $time)))
            ->orderByRaw('zone_id IS NOT NULL DESC')
            ->orderByRaw('dow IS NOT NULL DESC')
            ->orderByRaw('time_from IS NOT NULL DESC')
            ->first();
    }
}
