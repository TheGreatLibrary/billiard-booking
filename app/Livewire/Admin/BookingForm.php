<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\{User, Place, Zone, Resource, ProductModel, ProductType};
use App\Services\BookingService;

class BookingForm extends Component
{

    // Данные формы
    public $user_id;
    public $place_id;
    public $zone_id;
    public $resource_ids = [];
    public $starts_at;
    public $ends_at;
    public $status = 'confirmed';
    public $notes;

    // Данные для селектов
    public $users = [];
    public $places = [];
    public $zones = [];
    public $tables = [];
    public $equipment = [];
    
    // Выбранное оборудование
    public $selectedEquipment = [];

    public function mount()
    {
        $this->users = User::all();
        $this->places = Place::all();
        $this->starts_at = now()->format('Y-m-d\TH:i');
        $this->ends_at = now()->addHours(2)->format('Y-m-d\TH:i');
    }

    public function updatedPlaceId($value)
    {
        $this->zones = Zone::where('place_id', $value)->get();
        $this->equipment = []; //ProductModel::all(); // или фильтр по месту
        $this->zone_id = null;
        $this->tables = [];
        $this->resource_ids = [];
    }

   public function updatedZoneId($value)
    {
        // Получаем столы через более чистый запрос
        $tableType = ProductType::where('name', 'table')->first();
        $equipmentType = ProductType::where('name', 'equipment')->first();

        // Столы
        $this->tables = $tableType 
            ? Resource::where('zone_id', $value)
                ->where('state_id', 1)
                ->whereHas('model.type', function($query) use ($tableType) {
                    $query->where('id', $tableType->id);
                })
                ->with('model')
                ->get()
                ->map(fn($t) => [
                    'id' => $t->id,
                    'name' => $t->code ?: $t->model->name,
                    'description' => $t->model->name
                ])
            : [];

        // Оборудование
        $this->equipment = $equipmentType 
            ? $equipmentType->models()->get()->map(fn($e) => [
                'id' => $e->id,
                'name' => $e->name,
                'description' => $e->name,
                'price' => $e->base_price_hour ?? $e->base_price_each
            ])
            : [];

        $this->resource_ids = [];
    }

    public function addEquipment($modelId)
    {
        $model = ProductModel::find($modelId);
        
        $this->selectedEquipment[] = [
            'model_id' => $model->id,
            'name' => $model->name,
            'price' => $model->base_price_hour,
            'qty' => 1
        ];
    }

    public function removeEquipment($index)
    {
        unset($this->selectedEquipment[$index]);
        $this->selectedEquipment = array_values($this->selectedEquipment);
    }

    public function save(BookingService $bookingService)
    {
        $validated = $this->validate([
            'user_id' => 'required|exists:users,id',
            'place_id' => 'required|exists:places,id',
            'zone_id' => 'required|exists:zones,id',
            'resource_ids' => 'required|array|min:1',
            'resource_ids.*' => 'exists:resources,id',
            'starts_at' => 'required|date|after:now',
            'ends_at' => 'required|date|after:starts_at',
            'status' => 'required|in:pending,confirmed,canceled',
            'notes' => 'nullable|string|max:500',
        ]);

        $validated['equipment'] = $this->selectedEquipment;

        try {
            $booking = $bookingService->createBooking($validated);
            
            session()->flash('success', 'Бронирование создано!');
            return redirect()->route('admin.bookings.show', $booking);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Ошибка: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.booking-form')->layout('admin.layout.app-livewire');
    }
}
