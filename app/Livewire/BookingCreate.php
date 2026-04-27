<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\{Place, Resource, ProductModel, ProductType};
use App\Services\BookingService;
use Carbon\Carbon;

class BookingCreate extends Component
{
    // Шаги формы (НОВЫЙ ПОРЯДОК)
    // 1=место, 2=дата+время, 3=столы(мульти), 4=эквип, 5=данные, 6=оплата, 7=успех
    public $step = 1;
    
    // Данные
    public $place_id;
    public $date;
    public $selectedSlots = [];       // ['12:00', '13:00']
    public $selectedResources = [];   // [id1, id2, ...] — МУЛЬТИ-ВЫБОР
    public $equipment = [];
    
    // Клиентские данные
    public $guest_name;
    public $guest_email;
    public $guest_phone;
    public $comment;
    
    // Данные для отображения
    public $places = [];
    public $placeData = [];
    public $availableSlots = [];       // Общие слоты (на основе первого доступного стола для расчёта цен)
    public $availableResourceIds = []; // ID столов, свободных на все выбранные слоты
    public $resourcePrices = [];       // [resource_id => price] цены за каждый стол
    public $availableEquipment = [];
    public $totalAmount = 0;
    
    public $booking;

    public function mount()
    {
        $this->places = Place::all();
        $this->date = now()->format('Y-m-d');
    }

    // ===================== ШАГ 1: Место =====================

    public function selectPlace($placeId)
    {
        $this->place_id = $placeId;
        $this->loadPlaceData();
        $this->step = 2;
    }

    private function loadPlaceData()
    {
        $service = app(BookingService::class);
        $this->placeData = $service->getPlaceResources($this->place_id);
    }

    // ===================== ШАГ 2: Дата + Время =====================

    public function updatedDate()
    {
        $this->selectedSlots = [];
        $this->selectedResources = [];
        $this->availableResourceIds = [];
        $this->resourcePrices = [];
        $this->loadGenericSlots();
        $this->calculateTotal();
    }

    /**
     * Загрузить слоты на основе общей доступности (любой стол)
     * Показываем слоты с ценами "от" — минимальной ценой среди доступных столов
     */
    private function loadGenericSlots()
    {
        if (!$this->place_id || !$this->date) return;

        $service = app(BookingService::class);

        // Берём все активные столы и собираем общую картину слотов
        $allResources = Resource::where('place_id', $this->place_id)
            ->where('type', 'table')
            ->whereNotNull('grid_x')
            ->whereHas('state', fn($q) => $q->where('name', 'active'))
            ->with(['productModel', 'zone', 'place'])
            ->get();

        $mergedSlots = [];

        foreach ($allResources as $resource) {
            $resourceSlots = $service->getAvailableSlots($resource, $this->date);
            
            foreach ($resourceSlots as $time => $slot) {
                if (!isset($mergedSlots[$time])) {
                    $mergedSlots[$time] = [
                        'available' => false,
                        'price' => PHP_INT_MAX,
                        'datetime' => $slot['datetime'],
                    ];
                }

                // Слот доступен, если хотя бы один стол свободен
                if ($slot['available']) {
                    $mergedSlots[$time]['available'] = true;
                    // Минимальная цена
                    $mergedSlots[$time]['price'] = min($mergedSlots[$time]['price'], $slot['price']);
                }
            }
        }

        // Убираем PHP_INT_MAX для занятых слотов
        foreach ($mergedSlots as $time => &$slot) {
            if (!$slot['available']) {
                $slot['price'] = 0;
            }
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
        
        // При изменении слотов — пересчитываем доступные столы
        $this->refreshAvailableResources();
        $this->calculateTotal();
    }

    public function quickSelect($hours)
    {
        $this->selectedSlots = [];
        $availableTimes = array_keys(array_filter($this->availableSlots, fn($slot) => $slot['available']));
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

    /**
     * Обновить список доступных столов для выбранных слотов
     */
    private function refreshAvailableResources()
    {
        if (empty($this->selectedSlots) || !$this->place_id || !$this->date) {
            $this->availableResourceIds = [];
            return;
        }

        $service = app(BookingService::class);
        $this->availableResourceIds = $service->getAvailableResourcesForSlots(
            $this->place_id, $this->date, $this->selectedSlots
        );

        // Убираем из выбранных столов те, которые стали недоступны
        $this->selectedResources = array_values(
            array_intersect($this->selectedResources, $this->availableResourceIds)
        );

        // Пересчитываем цены для доступных столов
        $this->recalculateResourcePrices();
    }

    /**
     * Рассчитать цены для каждого доступного стола
     */
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

    public function proceedToTables()
    {
        $this->validate([
            'selectedSlots' => 'required|array|min:1',
        ], [
            'selectedSlots.required' => 'Выберите минимум 1 час',
            'selectedSlots.min' => 'Выберите минимум 1 час',
        ]);

        $this->refreshAvailableResources();
        $this->step = 3;
    }

    // ===================== ШАГ 3: Столы (мульти) =====================

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

    // ===================== ШАГ 4: Оборудование =====================

    private function loadAvailableEquipment()
    {
        if (!$this->place_id) return;
        
        $this->availableEquipment = Resource::where('place_id', $this->place_id)
            ->where('type', 'equipment')
            ->where('quantity', '>', 0)
            ->whereHas('state', fn($q) => $q->where('name', 'active'))
            ->with('productModel')
            ->get()
            ->map(function($resource) {
                return [
                    'resource_id' => $resource->id,
                    'model_id' => $resource->model_id,
                    'name' => $resource->productModel->name ?? 'Unknown',
                    'code' => $resource->code,
                    'price' => $resource->productModel->base_price_each ?? 0,
                    'available_qty' => $this->getAvailableEquipmentQty($resource),
                    'total_qty' => $resource->quantity,
                ];
            })
            ->filter(fn($eq) => $eq['available_qty'] > 0);
    }

    private function getAvailableEquipmentQty(Resource $resource)
    {
        if (empty($this->selectedSlots) || !$this->date) {
            return $resource->quantity;
        }
        
        $minAvailable = $resource->quantity;
        foreach ($this->selectedSlots as $time) {
            $available = $resource->getAvailableQuantity($this->date, $time);
            $minAvailable = min($minAvailable, $available);
        }
        return $minAvailable;
    }

    public function addEquipment($resourceId)
    {
        foreach ($this->equipment as $item) {
            if ($item['resource_id'] == $resourceId) {
                session()->flash('warning', 'Этот инвентарь уже добавлен');
                return;
            }
        }

        $equipmentItem = collect($this->availableEquipment)->firstWhere('resource_id', $resourceId);
        
        if (!$equipmentItem || $equipmentItem['available_qty'] < 1) {
            session()->flash('error', 'Инвентарь недоступен');
            return;
        }

        $this->equipment[] = [
            'resource_id' => $equipmentItem['resource_id'],
            'model_id' => $equipmentItem['model_id'],
            'name' => $equipmentItem['name'],
            'price' => $equipmentItem['price'],
            'qty' => 1,
            'max_qty' => $equipmentItem['available_qty'],
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
        
        $maxQty = $this->equipment[$index]['max_qty'] ?? 999;
        if ($qty > $maxQty) {
            $qty = $maxQty;
            session()->flash('warning', "Доступно только {$maxQty} единиц");
        }
        
        $this->equipment[$index]['qty'] = $qty;
        $this->calculateTotal();
    }

    public function skipEquipment()
    {
        $this->step = 5;
    }

    public function proceedToClientData()
    {
        $this->step = 5;
    }

    // ===================== ШАГ 5: Данные клиента =====================

    public function createPendingBooking(BookingService $service)
    {
        $userId = auth()->id();
        
        if (!$userId) {
            $this->validate([
                'guest_name' => 'required|string|max:255',
                'guest_email' => 'required|email|max:255',
                'guest_phone' => 'nullable|string|max:20',
            ]);

            $user = \App\Models\User::firstOrCreate(
                ['phone' => $this->guest_phone],
                [
                    'name' => $this->guest_name,
                    'email' => $this->guest_email,
                    'password' => null,
                ]
            );
            $userId = $user->id;
        }

        if (empty($this->selectedResources)) {
            session()->flash('error', 'Не выбраны столы');
            return;
        }

        if (empty($this->selectedSlots)) {
            session()->flash('error', 'Не выбрано время');
            return;
        }

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
                'data' => [
                    'resource_ids' => $this->selectedResources,
                    'date' => $this->date,
                    'slots' => $this->selectedSlots,
                ]
            ]);
            
            session()->flash('error', 'Ошибка создания бронирования: ' . $e->getMessage());
        }
    }

    // ===================== ШАГ 6: Оплата =====================

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

    // ===================== Вспомогательные =====================

    private function calculateTotal()
    {
        $total = 0;
        
        // Стоимость столов
        foreach ($this->selectedResources as $resourceId) {
            $total += $this->resourcePrices[$resourceId] ?? 0;
        }
        
        // Оборудование
        foreach ($this->equipment as $item) {
            $total += $item['price'] * $item['qty'];
        }
        
        $this->totalAmount = $total;
    }

    /**
     * Получить данные выбранных столов для отображения
     */
    public function getSelectedResourcesData(): array
    {
        if (empty($this->selectedResources) || empty($this->placeData['resources'])) {
            return [];
        }

        $resources = collect($this->placeData['resources']);
        $result = [];

        foreach ($this->selectedResources as $id) {
            $resource = $resources->firstWhere('id', $id);
            if ($resource) {
                $resource['price'] = $this->resourcePrices[$id] ?? 0;
                $result[] = $resource;
            }
        }

        return $result;
    }

    public function goBack()
    {
        if ($this->step > 1) {
            $this->step--;
            
            if ($this->step === 3) {
                $this->refreshAvailableResources();
            }
            if ($this->step === 4) {
                $this->loadAvailableEquipment();
            }
        }
    }

    public function render()
    {
        return view('livewire.User.stepper')->layout('layouts.app');
    }
}