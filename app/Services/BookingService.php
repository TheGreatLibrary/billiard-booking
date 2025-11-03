<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Resource;
use App\Models\ProductModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BookingService
{
   public function __construct(
        private PriceCalculator $priceCalculator
    ) {}

    /**
     * Создать бронирование с несколькими столами и оборудованием
     */
    public function createBooking(array $data): Booking
    {
        // принцип ACID (все или ничего)
        // 1. Создаём бронирование
        // 2. Добавляем столы
        // 3. Создаём заказ
        // 4. Добавляем order_items
        // 5. Добавляем оборудование
        // 6. Обновляем сумму

        return DB::transaction(function () use ($data) {
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
            $totalAmount = 0;
            $bookingResources = [];

            // 2. Добавляем столы
            foreach ($data['resource_ids'] as $resourceId) {
                $resource = Resource::with(['model', 'zone'])->findOrFail($resourceId);

                $priceData = $this->priceCalculator->calculateTablePrice(
                    $resource,
                    $startsAt,
                    $endsAt,
                    $data['place_id']
                );

                $bookingResource = $booking->bookingResources()->create([
                    'resource_id' => $resourceId,
                    'starts_at' => $data['starts_at'],
                    'ends_at' => $data['ends_at'],
                    ...$priceData, // PHP 8.1+ spread operator
                ]);

                $bookingResources[] = $bookingResource;
                $totalAmount += $priceData['amount'];
            }

            // 3. Создаём заказ
            $order = $booking->order()->create([
                'user_id' => $data['user_id'],
                'place_id' => $data['place_id'],
                'total_amount' => $totalAmount,
            ]);

            // 4. Order items для столов
            foreach ($bookingResources as $br) {
                $order->items()->create([
                    'type' => 'table_time',
                    'booking_resource_id' => $br->id,
                    'qty' => 1,
                    'price_each' => null,
                    'amount' => $br->amount,
                ]);
            }

            // 5. Добавляем оборудование
            if (!empty($data['equipment'])) {
                $totalAmount = $this->addEquipment($order, $data['equipment'], $totalAmount);
                $order->update(['total_amount' => $totalAmount]);
            }

            return $booking->load(['bookingResources', 'order']);
        });
    }

    /**
     * Добавить оборудование к заказу
     */
    private function addEquipment($order, array $equipment, int $currentTotal): int
    {
        foreach ($equipment as $item) {
            if (empty($item['model_id'])) continue;

            $productModel = ProductModel::findOrFail($item['model_id']);
            $qty = (int) $item['qty'];
            $priceEach = (int) $productModel->base_price_hour;
            $amount = $priceEach * $qty;

            $order->items()->create([
                'type' => 'equipment',
                'product_model_id' => $item['model_id'],
                'qty' => $qty,
                'price_each' => $priceEach,
                'amount' => $amount,
            ]);

            $currentTotal += $amount;
        }

        return $currentTotal;
    }
}
