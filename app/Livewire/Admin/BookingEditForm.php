<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\{Booking, User, Place, Zone, Resource, ProductModel};
use App\Services\BookingService;
use Carbon\Carbon;

class BookingEditForm extends Component
{
    public Booking $booking; // Передаётся из маршрута
    
    // Данные формы
    public $user_id;
    public $place_id;
    public $zone_id;
    public $resource_id; // Для редактирования только 1 стол (упрощённо)
    public $starts_at;
    public $ends_at;
    public $status;
    public $notes;

    // Данные для селектов
    public $users = [];
    public $places = [];
    public $zones = [];
    public $tables = [];

    public function mount(Booking $booking)
    {
        $this->booking = $booking->load(['user', 'place', 'bookingResources.resource.zone']);
        
        // Заполняем форму данными бронирования
        $this->user_id = $booking->user_id;
        $this->place_id = $booking->place_id;
        $this->starts_at = Carbon::parse($booking->starts_at)->format('Y-m-d\TH:i');
        $this->ends_at = Carbon::parse($booking->ends_at)->format('Y-m-d\TH:i');
        $this->status = $booking->status;
        $this->notes = $booking->comment;

        // Если есть booking_resources, берём первый
        $firstResource = $booking->bookingResources->first();
        if ($firstResource) {
            $this->zone_id = $firstResource->resource->zone_id;
            $this->resource_id = $firstResource->resource_id;
        }

        // Загружаем данные для селектов
        $this->users = User::all();
        $this->places = Place::all();
        $this->loadZones();
        $this->loadTables();
    }

    public function updatedPlaceId($value)
    {
        $this->loadZones();
        $this->zone_id = null;
        $this->tables = [];
        $this->resource_id = null;
    }

    public function updatedZoneId($value)
    {
        $this->loadTables();
        $this->resource_id = null;
    }

    private function loadZones()
    {
        if ($this->place_id) {
            $this->zones = Zone::where('place_id', $this->place_id)->get();
        } else {
            $this->zones = collect();
        }
    }

    private function loadTables()
    {
        if ($this->zone_id) {
            $this->tables = Resource::where('zone_id', $this->zone_id)
                ->where('state_id', 1)
                ->with('model')
                ->get()
                ->map(fn($t) => [
                    'id' => $t->id,
                    'name' => $t->code ?: $t->model->name,
                    'description' => $t->model->name
                ]);
        } else {
            $this->tables = collect();
        }
    }

    public function update()
    {
        $validated = $this->validate([
            'user_id' => 'required|exists:users,id',
            'place_id' => 'required|exists:places,id',
            'zone_id' => 'required|exists:zones,id',
            'resource_id' => 'required|exists:resources,id',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'status' => 'required|in:pending,confirmed,canceled,finished,no_show',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            // Обновляем основное бронирование
            $this->booking->update([
                'user_id' => $validated['user_id'],
                'place_id' => $validated['place_id'],
                'starts_at' => $validated['starts_at'],
                'ends_at' => $validated['ends_at'],
                'status' => $validated['status'],
                'comment' => $validated['notes'] ?? null,
            ]);

            // Если стол или время изменились - обновляем booking_resource
            $firstBookingResource = $this->booking->bookingResources->first();
            
            if ($firstBookingResource) {
                $needRecalculate = 
                    $firstBookingResource->resource_id != $validated['resource_id'] ||
                    $firstBookingResource->starts_at != $validated['starts_at'] ||
                    $firstBookingResource->ends_at != $validated['ends_at'];

                if ($needRecalculate) {
                    // Пересчитываем через PriceCalculator
                    $resource = Resource::with(['model', 'zone'])->findOrFail($validated['resource_id']);
                    $startsAt = Carbon::parse($validated['starts_at']);
                    $endsAt = Carbon::parse($validated['ends_at']);
                    
                    $priceCalculator = app(\App\Services\PriceCalculator::class);
                    $priceData = $priceCalculator->calculateTablePrice(
                        $resource,
                        $startsAt,
                        $endsAt,
                        $validated['place_id']
                    );

                    $firstBookingResource->update([
                        'resource_id' => $validated['resource_id'],
                        'starts_at' => $validated['starts_at'],
                        'ends_at' => $validated['ends_at'],
                        ...$priceData,
                    ]);

                    // Обновляем заказ
                    if ($this->booking->order) {
                        $this->booking->order->update([
                            'total_amount' => $priceData['amount'],
                        ]);

                        $orderItem = $this->booking->order->items()
                            ->where('type', 'table_time')
                            ->where('booking_resource_id', $firstBookingResource->id)
                            ->first();
                        
                        if ($orderItem) {
                            $orderItem->update(['amount' => $priceData['amount']]);
                        }
                    }
                }
            }

            session()->flash('success', 'Бронирование обновлено!');
            return redirect()->route('admin.bookings.show', $this->booking);

        } catch (\Exception $e) {
            session()->flash('error', 'Ошибка: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.booking-edit-form')->layout('admin.layout.app-livewire');
    }
}
