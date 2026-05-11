<?php 

namespace App\Services;

use App\Models\{Booking, Resource, ProductModel, Place, User, BookingSlot, BookingEquipment};
use App\Mail\{BookingConfirmed, BookingCancelled};
use Carbon\Carbon;
use Illuminate\Support\Facades\{DB, Mail};

class BookingService
{
    public function __construct(
        private PriceCalculator $priceCalculator
    ) {}

    /**
     * Получить данные места с ресурсами (только столы)
     */
    public function getPlaceResources(int $placeId): array
    {
        $place = Place::findOrFail($placeId);
        
        $resources = Resource::where('place_id', $placeId)
            ->where('type', 'table')
            ->whereNotNull('grid_x')
            ->whereHas('state', function($query) {
                $query->where('name', 'active');
            })
            ->with(['productModel', 'zone', 'state'])
            ->get()
            ->map(function($resource) {
                return [
                    'id' => $resource->id,
                    'code' => $resource->code,
                    'model_name' => $resource->productModel->name ?? 'Unknown',
                    'zone_name' => $resource->zone->name ?? null,
                    'grid_x' => $resource->grid_x,
                    'grid_y' => $resource->grid_y,
                    'grid_width' => $resource->grid_width,
                    'grid_height' => $resource->grid_height,
                    'rotation' => $resource->rotation,
                    'state' => $resource->state->name ?? 'unknown',
                ];
            });
        
        $zones = $place->zones()->get()->map(function($zone) {
            return [
                'id' => $zone->id,
                'name' => $zone->name,
                'price_coef' => $zone->price_coef,
                'color' => $zone->color ?? '#3B82F6',
                'coordinates' => $zone->coordinates,
            ];
        });
        
        return [
            'place' => [
                'id' => $place->id,
                'name' => $place->name,
                'address' => $place->address ?? '',
                'grid_width' => $place->grid_width,
                'grid_height' => $place->grid_height,
                'hall_image' => $place->hall_image,
            ],
            'resources' => $resources,
            'zones' => $zones,
        ];
    }

    /**
     * Получить доступные слоты для стола на дату
     */
    public function getAvailableSlots(Resource $resource, string $date): array
    {
        $slots = [];
        $startHour = 12;
        $endHour = 28;
        
        $currentDate = Carbon::parse($date);
        
        if (!$resource->relationLoaded('productModel')) {
            $resource->load(['productModel', 'zone', 'place']);
        }
        
        $bookedSlots = DB::table('booking_slots')
            ->join('bookings', 'bookings.id', '=', 'booking_slots.booking_id')
            ->where('booking_slots.resource_id', $resource->id)
            ->where('booking_slots.slot_date', $date)
            ->whereIn('bookings.payment_status', ['pending', 'paid'])
            ->pluck('slot_time')
            ->toArray();
        
        for ($hour = $startHour; $hour < $endHour; $hour++) {
            $actualHour = $hour >= 24 ? $hour - 24 : $hour;
            $time = sprintf('%02d:00', $actualHour);
            $slotDateTime = $currentDate->copy();
            
            if ($hour >= 24) {
                $slotDateTime->addDay();
            }
            $slotDateTime->setTime($actualHour, 0);
            
            $isAvailable = !in_array($time, $bookedSlots);
            
            // Слоты в прошлом недоступны
            if ($isAvailable && $slotDateTime->lte(Carbon::now())) {
                $isAvailable = false;
            }
            
            $price = 0;
            if ($isAvailable) {
                $slotStart = $slotDateTime->copy();
                $slotEnd = $slotDateTime->copy()->addHour();
                
                try {
                    $priceData = $this->priceCalculator->calculateTablePrice(
                        $resource, $slotStart, $slotEnd, $resource->place_id
                    );
                    $price = $priceData['amount'];
                } catch (\Exception $e) {
                    $basePrice = $resource->productModel->base_price_hour ?? 100000;
                    $zoneCoef = $resource->zone->price_coef ?? 1.0;
                    $price = (int)($basePrice * $zoneCoef);
                }
            }
            
            $slots[$time] = [
                'available' => $isAvailable,
                'price' => $price,
                'datetime' => $slotDateTime->toIso8601String(),
            ];
        }
        
        return $slots;
    }

    /**
     * Проверить доступность ресурса на выбранные слоты
     */
    public function isResourceAvailableForSlots(int $resourceId, string $date, array $slots): bool
    {
        $bookedSlots = DB::table('booking_slots')
            ->join('bookings', 'bookings.id', '=', 'booking_slots.booking_id')
            ->where('booking_slots.resource_id', $resourceId)
            ->where('booking_slots.slot_date', $date)
            ->whereIn('bookings.payment_status', ['pending', 'paid'])
            ->pluck('slot_time')
            ->toArray();
        
        foreach ($slots as $time) {
            if (in_array($time, $bookedSlots)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Получить доступные столы для заданных слотов на дату
     */
    public function getAvailableResourcesForSlots(int $placeId, string $date, array $slots): array
    {
        if (empty($slots)) return [];

        $allTableIds = Resource::where('place_id', $placeId)
            ->where('type', 'table')
            ->whereNotNull('grid_x')
            ->whereHas('state', fn($q) => $q->where('name', 'active'))
            ->pluck('id')
            ->toArray();

        $busyTableIds = DB::table('booking_slots')
            ->join('bookings', 'bookings.id', '=', 'booking_slots.booking_id')
            ->whereIn('booking_slots.resource_id', $allTableIds)
            ->where('booking_slots.slot_date', $date)
            ->whereIn('booking_slots.slot_time', $slots)
            ->whereIn('bookings.payment_status', ['pending', 'paid'])
            ->distinct()
            ->pluck('booking_slots.resource_id')
            ->toArray();

        return array_values(array_diff($allTableIds, $busyTableIds));
    }

    /**
     * Рассчитать стоимость для ресурса на выбранные слоты
     */
    public function calculateResourcePrice(Resource $resource, string $date, array $slots): int
    {
        if (!$resource->relationLoaded('productModel')) {
            $resource->load(['productModel', 'zone', 'place']);
        }

        $total = 0;
        $currentDate = Carbon::parse($date);

        foreach ($slots as $time) {
            $hour = (int) substr($time, 0, 2);
            $slotDateTime = $currentDate->copy()->setTime($hour, 0);
            $slotEnd = $slotDateTime->copy()->addHour();

            try {
                $priceData = $this->priceCalculator->calculateTablePrice(
                    $resource, $slotDateTime, $slotEnd, $resource->place_id
                );
                $total += $priceData['amount'];
            } catch (\Exception $e) {
                $basePrice = $resource->productModel->base_price_hour ?? 100000;
                $zoneCoef = $resource->zone->price_coef ?? 1.0;
                $total += (int)($basePrice * $zoneCoef);
            }
        }
        return $total;
    }

    /**
     * Создать мульти-стольное бронирование (pending)
     */
    public function createPendingBooking(array $data): Booking
    {
        if (empty($data['slots']) || count($data['slots']) < 1) {
            throw new \Exception('Необходимо выбрать минимум 1 час');
        }

        // Поддержка старого (resource_id) и нового (resource_ids) формата
        $resourceIds = $data['resource_ids'] ?? [];
        if (empty($resourceIds) && !empty($data['resource_id'])) {
            $resourceIds = [$data['resource_id']];
        }

        if (empty($resourceIds)) {
            throw new \Exception('Необходимо выбрать минимум 1 стол');
        }

        $date = $data['date'];
        $requestedSlots = $data['slots'];

        $resources = Resource::with(['productModel', 'zone', 'place'])
            ->whereIn('id', $resourceIds)->get();

        if ($resources->count() !== count($resourceIds)) {
            throw new \Exception('Один или несколько столов не найдены');
        }

        $placeIds = $resources->pluck('place_id')->unique();
        if ($placeIds->count() > 1) {
            throw new \Exception('Все столы должны быть из одного заведения');
        }

        // Проверяем доступность
        foreach ($resources as $resource) {
            if (!$this->isResourceAvailableForSlots($resource->id, $date, $requestedSlots)) {
                throw new \Exception("Стол {$resource->code} недоступен на выбранное время");
            }
        }

        $userId = $data['user_id'] ?? null;
        $guestData = [];
        if (!$userId) {
            $guestData = [
                'guest_name' => $data['guest_name'] ?? null,
                'guest_email' => $data['guest_email'] ?? null,
                'guest_phone' => $data['guest_phone'] ?? null,
            ];
        }

        // Считаем сумму
        $totalAmount = 0;
        $slotRecords = [];

        foreach ($resources as $resource) {
            $totalAmount += $this->calculateResourcePrice($resource, $date, $requestedSlots);

            foreach ($requestedSlots as $time) {
                $hour = (int) substr($time, 0, 2);
                $slotDateTime = Carbon::parse($date)->setTime($hour, 0);

                $slotRecords[] = [
                    'resource_id' => $resource->id,
                    'slot_date' => $date,
                    'slot_time' => $time,
                    'slot_datetime' => $slotDateTime,
                ];
            }
        }

        // resource_id = первый стол (для обратной совместимости)
        $booking = Booking::create([
            'user_id' => $userId,
            'place_id' => $resources->first()->place_id,
            'resource_id' => $resources->first()->id,
            'status' => 'pending',
            'payment_status' => 'pending',
            'total_amount' => $totalAmount,
            'comment' => $data['comment'] ?? null,
            'expires_at' => now()->addMinutes(3),
            'created_at' => now(),
            ...$guestData,
        ]);

        foreach ($slotRecords as $slot) {
            $booking->slots()->create($slot);
        }

        if (!empty($data['equipment'])) {
            $totalAmount = $this->addEquipment($booking, $data['equipment'], $totalAmount);
            $booking->update(['total_amount' => $totalAmount]);
        }

        return $booking->load(['slots', 'equipment', 'resource']);
    }

    private function addEquipment(Booking $booking, array $equipment, int $currentTotal): int
    {
        foreach ($equipment as $item) {
            if (empty($item['model_id'])) continue;

            $productModel = ProductModel::findOrFail($item['model_id']);
            $qty = (int) ($item['qty'] ?? 1);
            $priceEach = (int) ($productModel->base_price_each ?? $productModel->base_price_hour);
            $amount = $priceEach * $qty;

            $booking->equipment()->create([
                'product_model_id' => $item['model_id'],
                'qty' => $qty,
                'price_each' => $priceEach,
                'amount' => $amount,
            ]);

            $currentTotal += $amount;
        }
        return $currentTotal;
    }

    public function payBooking(Booking $booking, string $paymentMethod): Booking
    {
        if ($booking->payment_status !== 'pending') {
            throw new \Exception('Бронирование уже оплачено или отменено');
        }

        if ($booking->expires_at && $booking->expires_at->isPast()) {
            $this->cancelExpiredBooking($booking);
            throw new \Exception('Время бронирования истекло');
        }

        $booking->update([
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'payment_method' => $paymentMethod,
            'paid_at' => now(),
            'expires_at' => null,
        ]);

        $booking = $booking->fresh(['slots', 'place', 'user']);

        // Отправляем подтверждение на email
        $this->sendBookingEmail($booking, 'confirmed');

        return $booking;
    }

    public function cancelExpiredBooking(Booking $booking): void
    {
        $booking->update([
            'status' => 'canceled',
            'payment_status' => 'canceled',
        ]);

        $this->sendBookingEmail($booking, 'expired');
    }

    public function finishBooking(Booking $booking): Booking
    {
        if ($booking->payment_status !== 'paid') {
            throw new \Exception('Можно завершить только оплаченное бронирование');
        }
        $booking->update(['status' => 'finished']);
        return $booking->fresh();
    }

    public function refundBooking(Booking $booking): Booking
    {
        if ($booking->payment_status !== 'paid') {
            throw new \Exception('Можно вернуть только оплаченное бронирование');
        }
        $booking->update(['status' => 'canceled', 'payment_status' => 'refunded']);

        $this->sendBookingEmail($booking, 'refunded');

        return $booking->fresh();
    }

    /**
     * Отправить email-уведомление по бронированию
     */
    private function sendBookingEmail(Booking $booking, string $type): void
    {
        $email = $booking->getClientEmail();
        if (!$email) return;

        try {
            $booking->loadMissing(['slots', 'place', 'user']);

            match ($type) {
                'confirmed' => Mail::to($email)->send(new BookingConfirmed($booking)),
                'expired'   => Mail::to($email)->send(new BookingCancelled($booking, 'expired')),
                'canceled'  => Mail::to($email)->send(new BookingCancelled($booking, 'canceled')),
                'refunded'  => Mail::to($email)->send(new BookingCancelled($booking, 'refunded')),
                default     => null,
            };
        } catch (\Exception $e) {
            \Log::warning("Email notification failed for booking #{$booking->id}: " . $e->getMessage());
        }
    }

    public function cleanupExpiredBookings(): int
    {
        $expired = Booking::where('payment_status', 'pending')
            ->where('expires_at', '<', now())->get();

        foreach ($expired as $booking) {
            $this->cancelExpiredBooking($booking);
        }
        return $expired->count();
    }
}