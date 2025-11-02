<?php
// app/Http/Controllers/Admin/BookingController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBookingRequest;
use App\Http\Requests\Admin\UpdateBookingRequest;
use App\Models\{
    Booking, User, Place, Zone, Resource, ProductModel
};
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    protected BookingService $service;

    public function __construct(BookingService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bookings = Booking::with(['user', 'place'])->latest()->paginate(15);
        return view('admin.bookings.index', compact('bookings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();
        $places = Place::all();
        $zones = collect();
        $tables = collect();

        return view('admin.bookings.create', compact('users', 'places', 'zones', 'tables'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookingRequest $request)
    {
        $validated = $request->validated();

        try {
            $booking = $this->service->createBooking($validated);

            return redirect()->route('admin.bookings.index')
                ->with('success', "Бронирование создано! Столов: " . count($validated['resource_ids']) . ", Сумма: {$booking->order->total_amount} ₽");
        } catch (\Throwable $e) {
            Log::error('BookingController::store error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Ошибка создания бронирования: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        $booking->load([
            'user',
            'place',
            'bookingResources.resource.model',
            'bookingResources.resource.zone',
            'order.items.bookingResource',
            'order.items.productModel',
        ]);

        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * Get zones by place ID (AJAX)
     */
    public function getZones(Place $place)
    {
        try {
            $zones = $this->service->getZonesByPlace($place);
            return response()->json($zones);
        } catch (\Exception $e) {
            Log::error('Error getting zones: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }

    /**
     * Get tables by zone ID (AJAX)
     */
    public function getTables(Zone $zone)
    {
        try {
            Log::info('Loading tables for zone: ' . $zone->id);
            $tables = $this->service->getTablesByZone($zone);
            Log::info('Found tables: ' . json_encode($tables));
            return response()->json($tables);
        } catch (\Exception $e) {
            Log::error('Error loading tables: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }

    /**
     * Get equipment by place (AJAX)
     */
    public function getEquipment(Place $place)
    {
        try {
            Log::info('=== Loading equipment for place: ' . $place->id);
            $equipment = $this->service->getEquipmentByPlace($place);
            Log::info('Found equipment items: ' . $equipment->count());
            return response()->json($equipment);
        } catch (\Exception $e) {
            Log::error('Error loading equipment: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Booking $booking)
    {
        // загружаем связи для view
        $booking->load(['user', 'place', 'bookingResources.resource.zone']);

        $users = User::all();
        $places = Place::all();

        // Получаем зоны для выбранного места через сервис
        $zones = $this->service->getZonesByPlace($booking->place);

        // Получаем столы для первого resource (если есть) через сервис
        $firstResource = $booking->bookingResources->first();
        $tables = collect();

        if ($firstResource) {
            $zone = $firstResource->resource->zone ?? null;
            if ($zone) {
                $tables = $this->service->getTablesByZone($zone);
            } else {
                $tables = collect();
            }
        }

        return view('admin.bookings.edit', compact('booking', 'users', 'places', 'zones', 'tables'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookingRequest $request, Booking $booking)
    {
        $validated = $request->validated();

        try {
            $this->service->updateBooking($booking, $validated);

            return redirect()->route('admin.bookings.show', $booking)
                ->with('success', 'Бронирование успешно обновлено!');
        } catch (\Throwable $e) {
            Log::error('BookingController::update error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Ошибка обновления: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        try {
            // Проверяем статус - возможно не хотим удалять подтверждённые/завершённые
            if (in_array($booking->status, ['confirmed', 'finished'])) {
                return back()->with('error', 'Нельзя удалить подтверждённое или завершённое бронирование. Измените статус на "Отменено" или "Не пришёл".');
            }

            $bookingId = $booking->id;

            $booking->delete();

            return redirect()->route('admin.bookings.index')
                ->with('success', "Бронирование #{$bookingId} успешно удалено");
        } catch (\Exception $e) {
            Log::error('Booking deletion error: ' . $e->getMessage());
            return back()->with('error', 'Ошибка удаления: ' . $e->getMessage());
        }
    }
}
