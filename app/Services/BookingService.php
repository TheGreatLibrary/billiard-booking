<?php 

namespace App\Services;

use App\Models\{Booking, Resource, ProductModel, Place, User};
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function __construct(
        private PriceCalculator $priceCalculator
    ) {}

    /**
     * Получить доступные слоты для стола на дату
     * 
     * @param Resource $resource
     * @param string $date формат Y-m-d
     * @return array ['12:00' => ['available' => true, 'price' => 50000], ...]
     */
    public function getAvailableSlots(Resource $resource, string $date): array
    {
        $slots = [];
        $startHour = 12;
        $endHour = 28; // 04:00 следующего дня = 24 + 4
        
        $currentDate = Carbon::parse($date);
        
        // Получаем занятые слоты
        $bookedSlots = DB::table('booking_slots')
            ->join('bookings', 'bookings.id', '=', 'booking_slots.booking_id')
            ->where('bookings.resource_id', $resource->id)
            ->where('booking_slots.slot_date', $date)
            ->whereIn('bookings.payment_status', ['pending', 'paid']) // не учитываем отмененные
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
            
            // Проверяем доступность
            $isAvailable = !in_array($time, $bookedSlots);
            
            // Считаем цену через PriceCalculator
            $price = 0;
            if ($isAvailable) {
                $slotStart = $slotDateTime->copy();
                $slotEnd = $slotDateTime->copy()->addHour();
                
                $priceData = $this->priceCalculator->calculateTablePrice(
                    $resource,
                    $slotStart,
                    $slotEnd,
                    $resource->place_id
                );
                
                $price = $priceData['amount'];
            }
            
            $slots[$time] = [
                'available' => $isAvailable,
                'price' => $price, // руб*100
                'datetime' => $slotDateTime->toIso8601String(),
            ];
        }
        
        return $slots;
    }

    /**
     * Создать временное бронирование (pending)
     * 
     * @param array $data
     * @return Booking
     */
    public function createPendingBooking(array $data): Booking
    {
        // Валидация: минимум 1 час
        if (empty($data['slots']) || count($data['slots']) < 1) {
            throw new \Exception('Необходимо выбрать минимум 1 час');
        }

        $resource = Resource::with(['model', 'zone', 'place'])->findOrFail($data['resource_id']);
        
        // Проверяем доступность всех слотов
        $date = $data['date']; // Y-m-d
        $requestedSlots = $data['slots']; // ['12:00', '13:00', '15:00']
        
        $availableSlots = $this->getAvailableSlots($resource, $date);
        
        foreach ($requestedSlots as $time) {
            if (!isset($availableSlots[$time]) || !$availableSlots[$time]['available']) {
                throw new \Exception("Слот {$time} недоступен");
            }
        }
        
        // Определяем пользователя или гостя
        $userId = $data['user_id'] ?? null;
        $guestData = [];
        
        if (!$userId) {
            $guestData = [
                'guest_name' => $data['guest_name'] ?? null,
                'guest_email' => $data['guest_email'] ?? null,
                'guest_phone' => $data['guest_phone'] ?? null,
            ];
        }
        
        // Считаем общую сумму за столы
        $totalAmount = 0;
        $slotRecords = [];
        
        foreach ($requestedSlots as $time) {
            $totalAmount += $availableSlots[$time]['price'];
            $slotRecords[] = [
                'slot_date' => $date,
                'slot_time' => $time,
                'slot_datetime' => Carbon::parse($availableSlots[$time]['datetime']),
            ];
        }
        
        // Создаем бронирование
        $booking = Booking::create([
            'user_id' => $userId,
            'place_id' => $resource->place_id,
            'resource_id' => $resource->id,
            'status' => 'pending',
            'payment_status' => 'pending',
            'total_amount' => $totalAmount,
            'comment' => $data['comment'] ?? null,
            'expires_at' => now()->addMinutes(2), // TTL 30 минут
             'created_at' => now(),
            ...$guestData,
        ]);
        
        // Создаем слоты
        foreach ($slotRecords as $slot) {
            $booking->slots()->create($slot);
        }
        
        // Добавляем оборудование (если есть)
        if (!empty($data['equipment'])) {
            $totalAmount = $this->addEquipment($booking, $data['equipment'], $totalAmount);
            $booking->update(['total_amount' => $totalAmount]);
        }
        
        return $booking->load(['slots', 'equipment', 'resource']);
    }

    /**
     * Добавить оборудование к бронированию
     */
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

    /**
     * Оплатить бронирование
     * 
     * @param Booking $booking
     * @param string $paymentMethod 'card' | 'online'
     * @return Booking
     */
    public function payBooking(Booking $booking, string $paymentMethod): Booking
    {
        if ($booking->payment_status !== 'pending') {
            throw new \Exception('Бронирование уже оплачено или отменено');
        }

        // Проверяем, не истек ли срок
        if ($booking->expires_at && $booking->expires_at->isPast()) {
            $this->cancelExpiredBooking($booking);
            throw new \Exception('Время бронирования истекло');
        }

        $booking->update([
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'payment_method' => $paymentMethod,
            'paid_at' => now(),
            'expires_at' => null, // убираем TTL
        ]);

        return $booking->fresh();
    }

    /**
     * Отменить истекшее бронирование
     */
    public function cancelExpiredBooking(Booking $booking): void
    {
        $booking->update([
            'status' => 'canceled',
            'payment_status' => 'canceled',
        ]);
        
        // Можно удалить, если хотите освободить БД
        // $booking->delete();
    }

    /**
     * Завершить бронирование (когда время истекло)
     */
    public function finishBooking(Booking $booking): Booking
    {
        if ($booking->payment_status !== 'paid') {
            throw new \Exception('Можно завершить только оплаченное бронирование');
        }

        $booking->update(['status' => 'finished']);
        
        return $booking->fresh();
    }

    /**
     * Возврат средств
     */
    public function refundBooking(Booking $booking): Booking
    {
        if ($booking->payment_status !== 'paid') {
            throw new \Exception('Можно вернуть только оплаченное бронирование');
        }

        $booking->update([
            'status' => 'canceled',
            'payment_status' => 'refunded',
        ]);

        return $booking->fresh();
    }

    /**
     * Получить все столы для места с координатами на сетке
     */
    public function getPlaceResources(int $placeId): array
    {
        $place = Place::findOrFail($placeId);
        
        $resources = Resource::where('place_id', $placeId)
            ->where('state_id', 1) // только доступные
            ->whereNotNull('grid_x') // только с координатами
            ->with(['model', 'zone'])
            ->get()
            ->map(fn($r) => [
                'id' => $r->id,
                'code' => $r->code,
                'model_name' => $r->model->name,
                'zone_name' => $r->zone->name ?? null,
                'grid_x' => $r->grid_x,
                'grid_y' => $r->grid_y,
                'grid_width' => $r->grid_width,
                'grid_height' => $r->grid_height,
                'rotation' => $r->rotation, // 0, 90, 180, 270
            ]);

        return [
            'place' => [
                'id' => $place->id,
                'name' => $place->name,
                'grid_width' => $place->grid_width,
                'grid_height' => $place->grid_height,
                'hall_image' => $place->hall_image,
            ],
            'resources' => $resources,
        ];
    }

    /**
     * Автоочистка истекших бронирований (для планировщика)
     */
    public function cleanupExpiredBookings(): int
    {
        $expired = Booking::where('payment_status', 'pending')
            ->where('expires_at', '<', now())
            ->get();

        foreach ($expired as $booking) {
            $this->cancelExpiredBooking($booking);
        }

        return $expired->count();
    }

}