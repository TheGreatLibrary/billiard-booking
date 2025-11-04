<?php

namespace App\Services;

use App\Models\{Booking, Resource, ProductModel, Order};
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
        return DB::transaction(function () use ($data) {
            // 1. Создаём бронирование
            $booking = Booking::create([
                'user_id' => $data['user_id'],
                'place_id' => $data['place_id'],
                'starts_at' => $data['starts_at'],
                'ends_at' => $data['ends_at'],
                'status' => $data['status'] ?? 'pending',
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
                    ...$priceData,
                ]);

                $bookingResources[] = $bookingResource;
                $totalAmount += $priceData['amount'];
            }

            // 3. Создаём заказ (status = 'pending' по умолчанию)
            $order = $booking->order()->create([
                'user_id' => $data['user_id'],
                'place_id' => $data['place_id'],
                'total_amount' => $totalAmount,
                'status' => 'pending', // Ждёт оплаты
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

    /**
     * Оплатить заказ
     */
    public function payOrder(Order $order, string $paymentMethod): Order
    {
        if (!$order->canPay()) {
            throw new \Exception('Заказ уже оплачен или отменён');
        }

        DB::transaction(function () use ($order, $paymentMethod) {
            $order->update([
                'status' => 'paid',
                'payment_method' => $paymentMethod,
                'paid_at' => now(),
            ]);

            // Обновляем статус бронирования
            $order->booking->update(['status' => 'confirmed']);
        });

        return $order->fresh();
    }

    /**
     * Отменить заказ (возврат)
     */
    public function cancelOrder(Order $order): Order
    {
        if (!$order->isPaid()) {
            throw new \Exception('Можно отменить только оплаченный заказ');
        }

        DB::transaction(function () use ($order) {
            $order->update(['status' => 'refunded']);
            $order->booking->update(['status' => 'canceled']);
        });

        return $order->fresh();
    }

    /**
     * Обновить бронирование (только если не оплачено)
     */
    public function updateBooking(Booking $booking, array $data): Booking
    {
        if ($booking->order && !$booking->order->canEdit()) {
            throw new \Exception('Нельзя редактировать оплаченное бронирование');
        }

        return DB::transaction(function () use ($booking, $data) {
            // Удаляем старые ресурсы и заказ
            $booking->bookingResources()->delete();
            $booking->order?->delete();

            // Обновляем бронирование
            $booking->update([
                'user_id' => $data['user_id'],
                'place_id' => $data['place_id'],
                'starts_at' => $data['starts_at'],
                'ends_at' => $data['ends_at'],
                'status' => $data['status'] ?? $booking->status,
                'comment' => $data['notes'] ?? $booking->comment,
            ]);

            // Пересоздаём ресурсы и заказ
            $startsAt = Carbon::parse($data['starts_at']);
            $endsAt = Carbon::parse($data['ends_at']);
            $totalAmount = 0;
            $bookingResources = [];

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
                    ...$priceData,
                ]);

                $bookingResources[] = $bookingResource;
                $totalAmount += $priceData['amount'];
            }

            $order = $booking->order()->create([
                'user_id' => $data['user_id'],
                'place_id' => $data['place_id'],
                'total_amount' => $totalAmount,
                'status' => 'pending',
            ]);

            foreach ($bookingResources as $br) {
                $order->items()->create([
                    'type' => 'table_time',
                    'booking_resource_id' => $br->id,
                    'qty' => 1,
                    'price_each' => null,
                    'amount' => $br->amount,
                ]);
            }

            if (!empty($data['equipment'])) {
                $totalAmount = $this->addEquipment($order, $data['equipment'], $totalAmount);
                $order->update(['total_amount' => $totalAmount]);
            }

            return $booking->load(['bookingResources', 'order']);
        });
    }
}