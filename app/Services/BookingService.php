<?php

namespace App\Services;

use App\Models\{
    Booking, Resource, ProductModel, PriceRule, Zone, Place
};
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingService
{
    /**
     * Создание бронирования (раньше было в контроллере -> store)
     * Возвращает созданный Booking
     */
    public function createBooking(array $data): Booking
    {
        DB::beginTransaction();
        try {
            // 1. Создаём бронирование
            $booking = Booking::create([
                'user_id' => $data['user_id'],
                'place_id' => $data['place_id'],
                'starts_at' => $data['starts_at'],
                'ends_at' => $data['ends_at'],
                'status' => $data['status'],
                'comment' => $data['notes'] ?? null,
            ]);

            $startsAt = Carbon::parse($data['starts_at']);
            $endsAt = Carbon::parse($data['ends_at']);
            $minutes = $startsAt->diffInMinutes($endsAt);

            // Находим ценовое правило один раз
            $priceRule = $this->findPriceRule(
                $data['place_id'],
                $data['zone_id'],
                $startsAt
            );

            $ruleKind = $priceRule ? $priceRule->kind : 'coef';
            $ruleValue = $priceRule ? (float) $priceRule->value : 1.0;

            $totalAmount = 0;
            $bookingResources = [];

            // 2. Создаём booking_resource для каждого стола
            foreach ($data['resource_ids'] as $resourceId) {
                $resource = Resource::with(['model', 'zone'])->findOrFail($resourceId);

                $basePrice = (int) $resource->model->base_price_hour;
                $zoneCoef = (float) $resource->zone->price_coef;

                // Рассчитываем цену за этот стол
                if ($ruleKind === 'coef') {
                    $hourPrice = $basePrice * $zoneCoef * $ruleValue;
                } else {
                    $hourPrice = $ruleValue;
                }

                $amount = (int) round(($hourPrice / 60) * $minutes);
                $totalAmount += $amount;

                // Создаём запись booking_resource
                $bookingResource = $booking->bookingResources()->create([
                    'resource_id' => $resourceId,
                    'starts_at' => $data['starts_at'],
                    'ends_at' => $data['ends_at'],
                    'hour_price_snapshot' => $basePrice,
                    'rule_kind' => $ruleKind,
                    'rule_value' => $ruleValue,
                    'zone_coef_snapshot' => $zoneCoef,
                    'minutes' => $minutes,
                    'amount' => $amount,
                ]);

                $bookingResources[] = $bookingResource;
            }

            // 3. Создаём заказ
            $order = $booking->order()->create([
                'user_id' => $data['user_id'],
                'place_id' => $data['place_id'],
                'total_amount' => $totalAmount, // пока только столы
            ]);

            // 4. Создаём order_items для столов (type = 'table_time')
            foreach ($bookingResources as $br) {
                $order->items()->create([
                    'type' => 'table_time',
                    'booking_resource_id' => $br->id,
                    'qty' => 1,
                    'price_each' => null,
                    'amount' => $br->amount,
                ]);
            }

            // 5. Добавляем оборудование (type = 'equipment')
            if (!empty($data['equipment'])) {
                foreach ($data['equipment'] as $equipmentData) {
                    if (empty($equipmentData['model_id'])) {
                        continue; // пропускаем пустые строки
                    }

                    $productModel = ProductModel::findOrFail($equipmentData['model_id']);
                    $qty = (int) $equipmentData['qty'];
                    $priceEach = (int) $productModel->base_price_hour;
                    $equipmentAmount = $priceEach * $qty;

                    $order->items()->create([
                        'type' => 'equipment',
                        'booking_resource_id' => null,
                        'product_model_id' => $equipmentData['model_id'],
                        'qty' => $qty,
                        'price_each' => $priceEach,
                        'amount' => $equipmentAmount,
                    ]);

                    $totalAmount += $equipmentAmount;
                }

                // Обновляем общую сумму заказа
                $order->update(['total_amount' => $totalAmount]);
            }

            DB::commit();
            return $booking;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Booking creation failed: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Поиск подходящего ценового правила
     */
    public function findPriceRule($placeId, $zoneId, Carbon $datetime)
    {
        $dow = $datetime->dayOfWeek;
        $time = $datetime->format('H:i');

        return PriceRule::where('place_id', $placeId)
            ->where('active', 1)
            ->where(function($q) use ($zoneId) {
                $q->whereNull('zone_id')->orWhere('zone_id', $zoneId);
            })
            ->where(function($q) use ($dow) {
                $q->whereNull('dow')->orWhere('dow', $dow);
            })
            ->where(function($q) use ($time) {
                $q->whereNull('time_from')
                  ->orWhere(function($qq) use ($time) {
                      $qq->where('time_from', '<=', $time)
                         ->where('time_to', '>', $time);
                  });
            })
            ->orderByRaw('zone_id IS NOT NULL DESC')
            ->orderByRaw('dow IS NOT NULL DESC')
            ->orderByRaw('time_from IS NOT NULL DESC')
            ->first();
    }

    /**
     * AJAX: получить зоны по place
     */
    public function getZonesByPlace(Place $place)
    {
        return Zone::where('place_id', $place->id)->get();
    }

    /**
     * AJAX: получить столы по zone (подготовленный формат)
     */
    public function getTablesByZone(Zone $zone)
    {
        return Resource::join('product_models', 'resources.model_id', '=', 'product_models.id')
            ->where('resources.zone_id', $zone->id)
            ->where('resources.state_id', 1) // active
            ->select(
                'resources.id',
                'resources.code as name',
                'resources.note',
                'product_models.name as model_name'
            )
            ->get()
            ->map(function($table) {
                return [
                    'id' => $table->id,
                    'name' => $table->name ?: $table->model_name,
                    'description' => $table->model_name . ' - ' . ($table->note ?: 'Стол для бильярда')
                ];
            });
    }

    /**
     * AJAX: получить оборудование по place
     */
    public function getEquipmentByPlace(Place $place)
    {
        return ProductModel::select('id', 'name', 'base_price_hour as price')
            ->get();
    }

    /**
     * Обновление бронирования (раньше было в контроллере -> update)
     */
    public function updateBooking(Booking $booking, array $validated)
    {
        DB::beginTransaction();
        try {
            // 1. Обновляем основное бронирование
            $booking->update([
                'user_id' => $validated['user_id'],
                'place_id' => $validated['place_id'],
                'starts_at' => $validated['starts_at'],
                'ends_at' => $validated['ends_at'],
                'status' => $validated['status'],
                'comment' => $validated['notes'] ?? null,
            ]);

            // 2. Проверяем, изменился ли стол или время
            $firstBookingResource = $booking->bookingResources->first();

            $needRecalculate = false;
            if ($firstBookingResource) {
                $needRecalculate =
                    $firstBookingResource->resource_id != $validated['resource_id'] ||
                    $firstBookingResource->starts_at != $validated['starts_at'] ||
                    $firstBookingResource->ends_at != $validated['ends_at'];
            }

            if ($needRecalculate) {
                // Пересчитываем цену
                $resource = Resource::with(['model', 'zone'])->findOrFail($validated['resource_id']);
                $startsAt = Carbon::parse($validated['starts_at']);
                $endsAt = Carbon::parse($validated['ends_at']);
                $minutes = $startsAt->diffInMinutes($endsAt);

                $priceRule = $this->findPriceRule(
                    $validated['place_id'],
                    $validated['zone_id'],
                    $startsAt
                );

                $basePrice = (int) $resource->model->base_price_hour;
                $zoneCoef = (float) $resource->zone->price_coef;

                if ($priceRule) {
                    $ruleKind = $priceRule->kind;
                    $ruleValue = (float) $priceRule->value;
                } else {
                    $ruleKind = 'coef';
                    $ruleValue = 1.0;
                }

                if ($ruleKind === 'coef') {
                    $hourPrice = $basePrice * $zoneCoef * $ruleValue;
                } else {
                    $hourPrice = $ruleValue;
                }

                $amount = (int) round(($hourPrice / 60) * $minutes);

                // 3. Обновляем booking_resource
                if ($firstBookingResource) {
                    $firstBookingResource->update([
                        'resource_id' => $validated['resource_id'],
                        'starts_at' => $validated['starts_at'],
                        'ends_at' => $validated['ends_at'],
                        'hour_price_snapshot' => $basePrice,
                        'rule_kind' => $ruleKind,
                        'rule_value' => $ruleValue,
                        'zone_coef_snapshot' => $zoneCoef,
                        'minutes' => $minutes,
                        'amount' => $amount,
                    ]);

                    // 4. Обновляем заказ если есть
                    if ($booking->order) {
                        $booking->order->update([
                            'user_id' => $validated['user_id'],
                            'place_id' => $validated['place_id'],
                            'total_amount' => $amount,
                        ]);

                        // 5. Обновляем order_item
                        $orderItem = $booking->order->items()
                            ->where('type', 'table_time')
                            ->where('booking_resource_id', $firstBookingResource->id)
                            ->first();

                        if ($orderItem) {
                            $orderItem->update(['amount' => $amount]);
                        }
                    }
                }
            } else {
                // Если время/стол не изменились, просто обновляем user/place в заказе
                if ($booking->order) {
                    $booking->order->update([
                        'user_id' => $validated['user_id'],
                        'place_id' => $validated['place_id'],
                    ]);
                }
            }

            DB::commit();
            return $booking;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Booking update failed: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            throw $e;
        }
    }
}
