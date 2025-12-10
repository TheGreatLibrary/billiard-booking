<?php

namespace App\Livewire\Guest;

use Livewire\Component;
use App\Models\{Place, Resource, ProductModel, ProductType, User};
use App\Services\BookingService;
use Carbon\Carbon;

class BookingCreate extends Component
{
    // Шаги формы
    public $step = 1; // 1=место, 2=стол, 3=время, 4=эквип, 5=данные, 6=оплата
    
    // Данные
    public $place_id;
    public $resource_id;
    public $date;
    public $selectedSlots = []; // ['12:00', '13:00']
    public $equipment = []; // [['model_id' => 1, 'qty' => 2], ...]
    
    // Клиентские данные
    public $guest_name;
    public $guest_email;
    public $guest_phone;
    public $comment;
    
    // Данные для отображения
    public $places = [];
    public $placeData = []; // hall_width, hall_height, resources
    public $availableSlots = [];
    public $availableEquipment = [];
    public $totalAmount = 0;
    
    public $booking; // созданное pending бронирование

    public function mount()
    {
        $this->places = Place::all();
        $this->date = now()->format('Y-m-d');
    }

    // ШАГ 1: Выбор места
    public function selectPlace($placeId)
    {
        $this->place_id = $placeId;
        $this->loadPlaceData();
        $this->step = 2;
    }

    /**
     * Быстрый выбор нескольких часов подряд
     */
    public function quickSelect($hours)
    {
        $this->selectedSlots = [];
        
        $availableTimes = array_keys(array_filter($this->availableSlots, fn($slot) => $slot['available']));
        
        // Берем первые N доступных слотов
        $slotsToSelect = array_slice($availableTimes, 0, $hours);
        
        foreach ($slotsToSelect as $time) {
            $this->selectedSlots[] = $time;
        }
        
        $this->calculateTotal();
    }

    /**
     * Очистить выбранные слоты
     */
    public function clearSlots()
    {
        $this->selectedSlots = [];
        $this->calculateTotal();
    }

    /**
     * Переход к выбору времени (для кнопки на шаге 2)
     */
    public function proceedToTimeSelection()
    {
        if (!$this->resource_id) {
            session()->flash('error', 'Выберите стол');
            return;
        }
        
        $this->step = 3;
    }

    private function loadPlaceData()
    {
        $service = app(BookingService::class);
        $this->placeData = $service->getPlaceResources($this->place_id);
    }

    // ШАГ 2: Выбор стола на карте
    public function selectResource($resourceId)
    {
        $this->resource_id = $resourceId;
        $this->clearSlots();
        $this->loadAvailableSlots();
        $this->step = 3;
    }

    // ШАГ 3: Выбор времени
    public function updatedDate()
    {
        $this->loadAvailableSlots();
        $this->selectedSlots = [];
        $this->calculateTotal();
    }

    private function loadAvailableSlots()
    {
        if (!$this->resource_id || !$this->date) return;

        $resource = Resource::findOrFail($this->resource_id);
        $service = app(BookingService::class);
        $this->availableSlots = $service->getAvailableSlots($resource, $this->date);
    }

    public function toggleSlot($time)
    {
        if (in_array($time, $this->selectedSlots)) {
            $this->selectedSlots = array_values(array_diff($this->selectedSlots, [$time]));
        } else {
            if ($this->availableSlots[$time]['available']) {
                $this->selectedSlots[] = $time;
            }
        }
        
        sort($this->selectedSlots);
        $this->calculateTotal();
    }

    public function proceedToEquipment()
    {
        $this->validate([
            'selectedSlots' => 'required|array|min:1',
        ], [
            'selectedSlots.required' => 'Выберите минимум 1 час',
            'selectedSlots.min' => 'Выберите минимум 1 час',
        ]);

        $this->loadAvailableEquipment();
        $this->step = 4;
    }

    // ✅ ШАГ 4: Выбор оборудования (ИСПРАВЛЕНО)
    private function loadAvailableEquipment()
    {
        if (!$this->place_id) return;
        
        // ✅ Получаем только equipment ресурсы этого места
        $this->availableEquipment = Resource::where('place_id', $this->place_id)
            ->where('type', 'equipment')
            ->where('quantity', '>', 0)
            ->whereHas('state', function($q) {
                $q->where('name', 'active');
            })
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
            ->filter(fn($eq) => $eq['available_qty'] > 0); // Только с доступным количеством
    }

    /**
     * ✅ Получить доступное количество инвентаря для выбранных слотов
     */
    private function getAvailableEquipmentQty(Resource $resource)
    {
        if (empty($this->selectedSlots) || !$this->date) {
            return $resource->quantity;
        }
        
        $minAvailable = $resource->quantity;
        
        // Проверяем каждый выбранный слот
        foreach ($this->selectedSlots as $time) {
            $available = $resource->getAvailableQuantity($this->date, $time);
            $minAvailable = min($minAvailable, $available);
        }
        
        return $minAvailable;
    }

    public function addEquipment($resourceId)
    {
        // Проверяем, не добавлен ли уже
        foreach ($this->equipment as $item) {
            if ($item['resource_id'] == $resourceId) {
                session()->flash('warning', 'Этот инвентарь уже добавлен');
                return;
            }
        }

        $equipmentItem = collect($this->availableEquipment)->firstWhere('resource_id', $resourceId);
        
        if (!$equipmentItem) {
            session()->flash('error', 'Инвентарь не найден');
            return;
        }
        
        if ($equipmentItem['available_qty'] < 1) {
            session()->flash('error', 'Инвентарь недоступен на выбранное время');
            return;
        }

        $this->equipment[] = [
            'resource_id' => $equipmentItem['resource_id'],
            'model_id' => $equipmentItem['model_id'],
            'name' => $equipmentItem['name'],
            'price' => $equipmentItem['price'],
            'qty' => 1,
            'max_qty' => $equipmentItem['available_qty'], // Для валидации
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
        $qty = (int) $qty;
        
        if ($qty < 1) {
            $qty = 1;
        }
        else if ($qty>4) {
            $qty = 4;
        }
        
        // ✅ Проверяем максимальное доступное количество
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

    // ШАГ 5: Данные клиента и создание pending бронирования
    public function createPendingBooking(BookingService $service)
    {
        // Если пользователь авторизован
        $userId = auth()->id();
        
        if (!$userId) {
            // Валидация для гостя
            $this->validate([
                'guest_name' => 'required|string|max:255',
                'guest_email' => 'required|email|max:255',
                'guest_phone' => 'nullable|string|max:20',
            ]);

            $user = User::firstOrCreate(
            ['phone' => $this->guest_phone],
            [
                'name' => $this->guest_name,
                'email' => $this->guest_email,
                'password' => null, // без пароля — не может войти
            ]
        );
            $userId = $user->id;

        }

        if (!$this->resource_id) {
            session()->flash('error', 'Не выбран стол');
            return;
        }

        if (empty($this->selectedSlots)) {
            session()->flash('error', 'Не выбрано время');
            return;
        }

        try {
            $this->booking = $service->createPendingBooking([
                'user_id' => $userId,
                'resource_id' => $this->resource_id,
                'date' => $this->date,
                'slots' => $this->selectedSlots,
                'equipment' => $this->equipment,
                'guest_name' => $this->guest_name,
                'guest_email' => $this->guest_email,
                'guest_phone' => $this->guest_phone,
                'comment' => $this->comment,
            ]);

            $this->step = 6; // переход к оплате
            
        } catch (\Exception $e) {
            // Логируем полную ошибку
            \Log::error('Booking creation error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => [
                    'resource_id' => $this->resource_id,
                    'date' => $this->date,
                    'slots' => $this->selectedSlots,
                ]
            ]);
            
            session()->flash('error', 'Ошибка создания бронирования: ' . $e->getMessage());
        }
    }

    // ШАГ 6: Оплата
    public function payBooking(BookingService $service, $method)
    {
        try {
            $service->payBooking($this->booking, $method);
            
            // Просто показываем сообщение и остаёмся на странице
            session()->flash('success', 'Оплата прошла успешно! Бронирование подтверждено.');
            $this->step = 7; // Финальный шаг - успех
            
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function skipPayment()
    {
        session()->flash('info', 'Бронирование создано. Оплатите в течение 30 минут.');
        $this->step = 7; // Тоже переходим на финальный шаг
    }

    // Вспомогательные методы
    private function calculateTotal()
    {
        $total = 0;
        
        // Слоты
        foreach ($this->selectedSlots as $time) {
            if (isset($this->availableSlots[$time])) {
                $total += $this->availableSlots[$time]['price'];
            }
        }
        
        // Оборудование
        foreach ($this->equipment as $item) {
            $total += $item['price'] * $item['qty'];
        }
        
        $this->totalAmount = $total;
    }

    public function goBack()
    {
        if ($this->step > 1) {
            $this->step--;
            
            // При возврате на шаг 4 - перезагружаем доступность инвентаря
            if ($this->step === 4) {
                $this->loadAvailableEquipment();
            }
        }
    }
    public function render()
    {
        return view('livewire.guest.booking-create')->layout('layouts.guest');
    }
}
