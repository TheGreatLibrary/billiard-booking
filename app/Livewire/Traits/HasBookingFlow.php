<?php

namespace App\Livewire\Traits;

use App\Models\{Place, Resource, ProductModel, ProductType, User};
use App\Services\BookingService;
use Carbon\Carbon;

trait HasBookingFlow
{
    // Шаги: 1=место, 2=время, 3=столы, 4=эквип, 5=данные, 6=оплата, 7=успех
    public $step = 1;

    public $place_id;
    public $date;
    public $selectedSlots = [];
    public $selectedResources = [];
    public $equipment = [];

    public $guest_name;
    public $guest_email;
    public $guest_phone;
    public $comment;

    public $places = [];
    public $placeData = [];
    public $availableSlots = [];
    public $availableResourceIds = [];
    public $resourcePrices = [];
    public $availableEquipment = [];
    public $totalAmount = 0;

    public $booking;

    // ==================== Инициализация ====================

    public function mountBookingFlow()
    {
        $this->places = Place::all();
        $this->date = now()->format('Y-m-d');
    }

    // ==================== Шаг 1: Место ====================

    public function selectPlace($placeId)
    {
        $this->place_id = $placeId;
        $this->loadPlaceData();
        $this->loadGenericSlots();
        $this->step = 2;
    }

    private function loadPlaceData()
    {
        $this->placeData = app(BookingService::class)->getPlaceResources($this->place_id);
    }

    // ==================== Шаг 2: Дата + Время ====================

    public function updatedDate()
    {
        $this->selectedSlots = [];
        $this->selectedResources = [];
        $this->availableResourceIds = [];
        $this->resourcePrices = [];
        $this->loadGenericSlots();
        $this->calculateTotal();
    }

    private function loadGenericSlots()
    {
        if (!$this->place_id || !$this->date) return;

        $service = app(BookingService::class);
        $allResources = Resource::where('place_id', $this->place_id)
            ->where('type', 'table')->whereNotNull('grid_x')
            ->whereHas('state', fn($q) => $q->where('name', 'active'))
            ->with(['productModel', 'zone', 'place'])->get();

        $mergedSlots = [];
        foreach ($allResources as $resource) {
            foreach ($service->getAvailableSlots($resource, $this->date) as $time => $slot) {
                if (!isset($mergedSlots[$time])) {
                    $mergedSlots[$time] = ['available' => false, 'price' => PHP_INT_MAX, 'datetime' => $slot['datetime']];
                }
                if ($slot['available']) {
                    $mergedSlots[$time]['available'] = true;
                    $mergedSlots[$time]['price'] = min($mergedSlots[$time]['price'], $slot['price']);
                }
            }
        }

        foreach ($mergedSlots as &$slot) {
            if (!$slot['available']) $slot['price'] = 0;
        }

        $this->availableSlots = $mergedSlots;
    }

    public function toggleSlot($time)
    {
        if (in_array($time, $this->selectedSlots)) {
            $this->selectedSlots = array_values(array_diff($this->selectedSlots, [$time]));
        } else {
            if (isset($this->availableSlots[$time]) && $this->availableSlots[$time]['available']) {
                $this->selectedSlots[] = $time;
            }
        }
        sort($this->selectedSlots);
        $this->refreshAvailableResources();
        $this->calculateTotal();
    }

    public function quickSelect($hours)
    {
        $this->selectedSlots = [];
        $availableTimes = array_keys(array_filter($this->availableSlots, fn($s) => $s['available']));
        $this->selectedSlots = array_slice($availableTimes, 0, $hours);
        $this->refreshAvailableResources();
        $this->calculateTotal();
    }

    public function clearSlots()
    {
        $this->selectedSlots = [];
        $this->selectedResources = [];
        $this->availableResourceIds = [];
        $this->resourcePrices = [];
        $this->calculateTotal();
    }

    public function proceedToTables()
    {
        $this->validate(['selectedSlots' => 'required|array|min:1'], [
            'selectedSlots.required' => 'Выберите минимум 1 час',
            'selectedSlots.min' => 'Выберите минимум 1 час',
        ]);
        $this->refreshAvailableResources();
        $this->step = 3;
    }

    // ==================== Шаг 3: Столы ====================

    private function refreshAvailableResources()
    {
        if (empty($this->selectedSlots) || !$this->place_id || !$this->date) {
            $this->availableResourceIds = [];
            return;
        }
        $this->availableResourceIds = app(BookingService::class)->getAvailableResourcesForSlots(
            $this->place_id, $this->date, $this->selectedSlots
        );
        $this->selectedResources = array_values(
            array_intersect($this->selectedResources, $this->availableResourceIds)
        );
        $this->recalculateResourcePrices();
    }

    private function recalculateResourcePrices()
    {
        $this->resourcePrices = [];
        if (empty($this->availableResourceIds) || empty($this->selectedSlots)) return;

        $service = app(BookingService::class);
        $resources = Resource::with(['productModel', 'zone', 'place'])
            ->whereIn('id', $this->availableResourceIds)->get();

        foreach ($resources as $resource) {
            $this->resourcePrices[$resource->id] = $service->calculateResourcePrice(
                $resource, $this->date, $this->selectedSlots
            );
        }
    }

    public function toggleResource($resourceId)
    {
        $resourceId = (int) $resourceId;
        if (!in_array($resourceId, $this->availableResourceIds)) {
            session()->flash('error', 'Этот стол недоступен на выбранное время');
            return;
        }
        if (in_array($resourceId, $this->selectedResources)) {
            $this->selectedResources = array_values(array_diff($this->selectedResources, [$resourceId]));
        } else {
            $this->selectedResources[] = $resourceId;
        }
        $this->calculateTotal();
    }

    public function proceedToEquipment()
    {
        if (empty($this->selectedResources)) {
            session()->flash('error', 'Выберите минимум 1 стол');
            return;
        }
        $this->loadAvailableEquipment();
        $this->step = 4;
    }

    // ==================== Шаг 4: Оборудование ====================

    private function loadAvailableEquipment()
    {
        if (!$this->place_id) return;

        $this->availableEquipment = Resource::where('place_id', $this->place_id)
            ->where('type', 'equipment')->where('quantity', '>', 0)
            ->whereHas('state', fn($q) => $q->where('name', 'active'))
            ->with('productModel')->get()
            ->map(fn($r) => [
                'resource_id' => $r->id, 'model_id' => $r->model_id,
                'name' => $r->productModel->name ?? 'Unknown', 'code' => $r->code,
                'price' => $r->productModel->base_price_each ?? 0,
                'available_qty' => $this->getAvailableEquipmentQty($r),
                'total_qty' => $r->quantity,
            ])->filter(fn($eq) => $eq['available_qty'] > 0);
    }

    private function getAvailableEquipmentQty(Resource $resource)
    {
        if (empty($this->selectedSlots) || !$this->date) return $resource->quantity;
        $min = $resource->quantity;
        foreach ($this->selectedSlots as $time) {
            $min = min($min, $resource->getAvailableQuantity($this->date, $time));
        }
        return $min;
    }

    public function addEquipment($resourceId)
    {
        foreach ($this->equipment as $item) {
            if ($item['resource_id'] == $resourceId) {
                session()->flash('warning', 'Уже добавлен');
                return;
            }
        }
        $eq = collect($this->availableEquipment)->firstWhere('resource_id', $resourceId);
        if (!$eq || $eq['available_qty'] < 1) {
            session()->flash('error', 'Недоступен');
            return;
        }
        $this->equipment[] = [
            'resource_id' => $eq['resource_id'], 'model_id' => $eq['model_id'],
            'name' => $eq['name'], 'price' => $eq['price'],
            'qty' => 1, 'max_qty' => $eq['available_qty'],
        ];
        $this->calculateTotal();
    }

    public function removeEquipment($index)
    {
        unset($this->equipment[$index]);
        $this->equipment = array_values($this->equipment);
        $this->calculateTotal();
    }

    public function updateEquipmentQty($index, $qty)
    {
        $qty = max(1, min(4, (int) $qty));
        $max = $this->equipment[$index]['max_qty'] ?? 999;
        if ($qty > $max) {
            $qty = $max;
            session()->flash('warning', "Доступно только {$max}");
        }
        $this->equipment[$index]['qty'] = $qty;
        $this->calculateTotal();
    }

    public function skipEquipment() { $this->step = 5; }
    public function proceedToClientData() { $this->step = 5; }

    // ==================== Шаг 5: Данные + создание ====================

    public function createPendingBooking(BookingService $service)
    {
        $userId = auth()->id();

        if (!$userId) {
            $this->validate([
                'guest_name' => 'required|string|max:255',
                'guest_email' => 'required|email|max:255',
                'guest_phone' => 'nullable|string|max:20',
            ]);
            $user = User::firstOrCreate(
                ['phone' => $this->guest_phone],
                ['name' => $this->guest_name, 'email' => $this->guest_email, 'password' => null]
            );
            $userId = $user->id;
        }

        if (empty($this->selectedResources)) { session()->flash('error', 'Не выбраны столы'); return; }
        if (empty($this->selectedSlots)) { session()->flash('error', 'Не выбрано время'); return; }

        try {
            $this->booking = $service->createPendingBooking([
                'user_id' => $userId,
                'resource_ids' => $this->selectedResources,
                'date' => $this->date,
                'slots' => $this->selectedSlots,
                'equipment' => $this->equipment,
                'guest_name' => $this->guest_name,
                'guest_email' => $this->guest_email,
                'guest_phone' => $this->guest_phone,
                'comment' => $this->comment,
            ]);
            $this->step = 6;
        } catch (\Exception $e) {
            \Log::error('Booking creation error', [
                'message' => $e->getMessage(),
                'resource_ids' => $this->selectedResources,
            ]);
            session()->flash('error', 'Ошибка: ' . $e->getMessage());
        }
    }

    // ==================== Шаг 6: Оплата ====================

    public function payBooking($method)
    {
        try {
            $service = app(BookingService::class);
            $service->payBooking($this->booking, $method);
            session()->flash('success', 'Оплата прошла успешно! Бронирование подтверждено.');
            $this->step = 7;
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function skipPayment()
    {
        session()->flash('info', 'Бронирование создано. Оплатите в течение 30 минут.');
        $this->step = 7;
    }

    // ==================== Общие ====================

    private function calculateTotal()
    {
        $total = 0;
        foreach ($this->selectedResources as $rid) {
            $total += $this->resourcePrices[$rid] ?? 0;
        }
        foreach ($this->equipment as $item) {
            $total += $item['price'] * $item['qty'];
        }
        $this->totalAmount = $total;
    }

    public function getSelectedResourcesData(): array
    {
        if (empty($this->selectedResources) || empty($this->placeData['resources'])) return [];

        $resources = collect($this->placeData['resources']);
        $result = [];
        foreach ($this->selectedResources as $id) {
            $r = $resources->firstWhere('id', $id);
            if ($r) {
                $r['price'] = $this->resourcePrices[$id] ?? 0;
                $result[] = $r;
            }
        }
        return $result;
    }

    public function goBack()
    {
        if ($this->step > 1) {
            $this->step--;
            if ($this->step === 3) $this->refreshAvailableResources();
            if ($this->step === 4) $this->loadAvailableEquipment();
        }
    }
}