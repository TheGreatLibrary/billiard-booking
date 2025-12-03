<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\{Place, Resource, ProductModel, ProductType};
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

    // ШАГ 4: Выбор оборудования
    private function loadAvailableEquipment()
    {
        $equipmentType = ProductType::where('name', 'equipment')->first();
        
        if ($equipmentType) {
            $this->availableEquipment = $equipmentType->models()
                ->get()
                ->map(fn($e) => [
                    'id' => $e->id,
                    'name' => $e->name,
                    'price' => $e->base_price_each ?? $e->base_price_hour,
                ]);
        }
    }

    public function addEquipment($modelId)
    {
        // Проверяем, не добавлен ли уже
        foreach ($this->equipment as $item) {
            if ($item['model_id'] == $modelId) {
                return; // уже добавлен
            }
        }

        $model = ProductModel::findOrFail($modelId);
        $this->equipment[] = [
            'model_id' => $model->id,
            'name' => $model->name,
            'price' => $model->base_price_each ?? $model->base_price_hour,
            'qty' => 1,
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
        if ($qty < 1) $qty = 1;
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
        }
    }

    public function render()
    {
        return view('livewire.User.booking-create')->layout('layouts.app');
    }
}