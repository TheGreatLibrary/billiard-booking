<?php
// app/Http\Controllers\Admin\BookingController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Models\Place;
use App\Models\Zone;
use App\Models\Resource;
use App\Models\BookingResource;
use App\Models\ProductModel;
use App\Models\PriceRule;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bookings = Booking::with(['user', 'place'])
            ->latest()
            ->paginate(15);

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
    public function store(Request $request)
{
    $validated = $request->validate([
        'user_id' => 'required|exists:users,id',
        'place_id' => 'required|exists:places,id',
        'zone_id' => 'required|exists:zones,id',
        'resource_ids' => 'required|array|min:1', // МАССИВ столов
        'resource_ids.*' => 'exists:resources,id',
        'starts_at' => 'required|date|after:now',
        'ends_at' => 'required|date|after:starts_at',
        'status' => 'required|in:pending,confirmed,canceled,finished,no_show',
        'notes' => 'nullable|string|max:500',
        'equipment' => 'nullable|array',
        'equipment.*.model_id' => 'required|exists:product_models,id',
        'equipment.*.qty' => 'required|integer|min:1',
    ]);

    try {
        DB::beginTransaction();

        // 1. Создаём бронирование
        $booking = Booking::create([
            'user_id' => $validated['user_id'],
            'place_id' => $validated['place_id'],
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'],
            'status' => $validated['status'],
            'comment' => $validated['notes'] ?? null,
        ]);

        $startsAt = Carbon::parse($validated['starts_at']);
        $endsAt = Carbon::parse($validated['ends_at']);
        $minutes = $startsAt->diffInMinutes($endsAt);

        // Находим ценовое правило один раз
        $priceRule = $this->findPriceRule(
            $validated['place_id'],
            $validated['zone_id'],
            $startsAt
        );

        $ruleKind = $priceRule ? $priceRule->kind : 'coef';
        $ruleValue = $priceRule ? (float) $priceRule->value : 1.0;

        $totalAmount = 0;
        $bookingResources = [];

        // 2. Создаём booking_resource для КАЖДОГО стола
        foreach ($validated['resource_ids'] as $resourceId) {
            $resource = Resource::with(['model', 'zone'])->findOrFail($resourceId);

            $basePrice = (int) $resource->model->base_price_hour;
            $zoneCoef = (float) $resource->zone->price_coef;

            // Рассчитываем цену за этот стол
            if ($ruleKind === 'coef') {
                $hourPrice = $basePrice * $zoneCoef * $ruleValue;
            } else {
                $hourPrice = $ruleValue;
            }

            $amount = (int) round(($hourPrice / 60) * $minutes);
            $totalAmount += $amount;

            // Создаём запись booking_resource
            $bookingResource = $booking->bookingResources()->create([
                'resource_id' => $resourceId,
                'starts_at' => $validated['starts_at'],
                'ends_at' => $validated['ends_at'],
                'hour_price_snapshot' => $basePrice,
                'rule_kind' => $ruleKind,
                'rule_value' => $ruleValue,
                'zone_coef_snapshot' => $zoneCoef,
                'minutes' => $minutes,
                'amount' => $amount,
            ]);

            $bookingResources[] = $bookingResource;
        }

        // 3. Создаём заказ
        $order = $booking->order()->create([
            'user_id' => $validated['user_id'],
            'place_id' => $validated['place_id'],
            'total_amount' => $totalAmount, // пока только столы
        ]);

        // 4. Создаём order_items для столов (type = 'table_time')
        foreach ($bookingResources as $br) {
            $order->items()->create([
                'type' => 'table_time',
                'booking_resource_id' => $br->id,
                'qty' => 1,
                'price_each' => null,
                'amount' => $br->amount,
            ]);
        }

        // 5. Добавляем оборудование (type = 'equipment')
        if (!empty($validated['equipment'])) {
            foreach ($validated['equipment'] as $equipmentData) {
                if (empty($equipmentData['model_id'])) {
                    continue; // пропускаем пустые строки
                }

                $productModel = ProductModel::findOrFail($equipmentData['model_id']);
                $qty = (int) $equipmentData['qty'];
                $priceEach = (int) $productModel->base_price_hour;
                $equipmentAmount = $priceEach * $qty;

                $order->items()->create([
                    'type' => 'equipment',
                    'booking_resource_id' => null,
                    'product_model_id' => $equipmentData['model_id'],
                    'qty' => $qty,
                    'price_each' => $priceEach,
                    'amount' => $equipmentAmount,
                ]);

                $totalAmount += $equipmentAmount;
            }

            // Обновляем общую сумму заказа
            $order->update(['total_amount' => $totalAmount]);
        }

        DB::commit();

        return redirect()->route('admin.bookings.index')
            ->with('success', "Бронирование создано! Столов: " . count($validated['resource_ids']) . ", Сумма: {$totalAmount} ₽");

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Booking creation error: ' . $e->getMessage());
        Log::error($e->getTraceAsString());
        
        return back()
            ->withInput()
            ->with('error', 'Ошибка создания бронирования: ' . $e->getMessage());
    }
}

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
 * Поиск подходящего ценового правила
 */
private function findPriceRule($placeId, $zoneId, $datetime)
{
    $dow = $datetime->dayOfWeek;
    $time = $datetime->format('H:i');

    return PriceRule::where('place_id', $placeId)
        ->where('active', 1)
        ->where(function($q) use ($zoneId) {
            $q->whereNull('zone_id')
              ->orWhere('zone_id', $zoneId);
        })
        ->where(function($q) use ($dow) {
            $q->whereNull('dow')
              ->orWhere('dow', $dow);
        })
        ->where(function($q) use ($time) {
            $q->whereNull('time_from')
              ->orWhere(function($qq) use ($time) {
                  $qq->where('time_from', '<=', $time)
                     ->where('time_to', '>', $time);
              });
        })
        ->orderByRaw('zone_id IS NOT NULL DESC')
        ->orderByRaw('dow IS NOT NULL DESC')
        ->orderByRaw('time_from IS NOT NULL DESC')
        ->first();
}
    /**
     * Get zones by place ID (AJAX)
     */
    public function getZones(Place $place)
    {
        try {
            $zones = Zone::where('place_id', $place->id)->get();
            return response()->json($zones);
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }




    /**

 * Get tables by zone ID (AJAX)
 */
 public function getTables(Zone $zone)
{
    try {
        \Log::info('Loading tables for zone: ' . $zone->id);
        
        // Простой запрос как вы сказали
        $tables = Resource::join('product_models', 'resources.model_id', '=', 'product_models.id')
            ->where('resources.zone_id', $zone->id)
            ->where('resources.state_id', 1) // active
            ->select(
                'resources.id',
                'resources.code as name', 
                'resources.note',
                'product_models.name as model_name'
            )
            ->get()
            ->map(function($table) {
                return [
                    'id' => $table->id,
                    'name' => $table->name ?: $table->model_name,
                    'description' => $table->model_name . ' - ' . ($table->note ?: 'Стол для бильярда')
                ];
            });
            
        \Log::info('Found tables: ' . json_encode($tables));
        return response()->json($tables);
        
    } catch (\Exception $e) {
        \Log::error('Error loading tables: ' . $e->getMessage());
        return response()->json([], 500);
    }
}
    // ... остальные методы остаются без изменений


    /**
 * Remove the specified resource from storage.
 */
public function destroy(Booking $booking)
{
    try {
        DB::beginTransaction();

        // Проверяем статус - возможно не хотим удалять подтверждённые/завершённые
        if (in_array($booking->status, ['confirmed', 'finished'])) {
            return back()->with('error', 'Нельзя удалить подтверждённое или завершённое бронирование. Измените статус на "Отменено" или "Не пришёл".');
        }

        $bookingId = $booking->id;

        // Laravel автоматически удалит связанные записи благодаря ON DELETE CASCADE в БД:
        // - booking_resources
        // - order (и все order_items через каскад)
        $booking->delete();

        DB::commit();

        return redirect()->route('admin.bookings.index')
            ->with('success', "Бронирование #{$bookingId} успешно удалено");

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Booking deletion error: ' . $e->getMessage());
        
        return back()->with('error', 'Ошибка удаления: ' . $e->getMessage());
    }
}

public function edit(Booking $booking)
{
    $booking->load(['user', 'place', 'bookingResources.resource.zone']);
    
    $users = User::all();
    $places = Place::all();
    
    // Получаем зоны для выбранного места
    $zones = Zone::where('place_id', $booking->place_id)->get();
    
    // Получаем столы для первого resource (если несколько - берём первый)
    $firstResource = $booking->bookingResources->first();
    $tables = collect();
    
    if ($firstResource) {
        $tables = Resource::where('zone_id', $firstResource->resource->zone_id)
            ->where('state_id', 1)
            ->with('model')
            ->get()
            ->map(function($table) {
                return [
                    'id' => $table->id,
                    'name' => $table->code ?: $table->model->name,
                    'description' => $table->model->name . ' - ' . ($table->note ?: 'Стол для бильярда')
                ];
            });
    }
    
    return view('admin.bookings.edit', compact('booking', 'users', 'places', 'zones', 'tables'));
}

public function getEquipment(Place $place)
{
    try {
        \Log::info('=== Loading equipment for place: ' . $place->id);
        
        // Загружаем ВСЕ модели товаров
        $equipment = ProductModel::select('id', 'name', 'base_price_hour as price')
            ->get();
            
        \Log::info('Found equipment items: ' . $equipment->count());
        \Log::info('Equipment data: ' . json_encode($equipment));
            
        return response()->json($equipment);
    } catch (\Exception $e) {
        \Log::error('Error loading equipment: ' . $e->getMessage());
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
/**
 * Update the specified resource in storage.
 */
public function update(Request $request, Booking $booking)
{
    $validated = $request->validate([
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
        DB::beginTransaction();

        // 1. Обновляем основное бронирование
        $booking->update([
            'user_id' => $validated['user_id'],
            'place_id' => $validated['place_id'],
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'],
            'status' => $validated['status'],
            'comment' => $validated['notes'] ?? null,
        ]);

        // 2. Проверяем, изменился ли стол или время
        $firstBookingResource = $booking->bookingResources->first();
        
        $needRecalculate = false;
        if ($firstBookingResource) {
            $needRecalculate = 
                $firstBookingResource->resource_id != $validated['resource_id'] ||
                $firstBookingResource->starts_at != $validated['starts_at'] ||
                $firstBookingResource->ends_at != $validated['ends_at'];
        }

        if ($needRecalculate) {
            // Пересчитываем цену
            $resource = Resource::with(['model', 'zone'])->findOrFail($validated['resource_id']);
            $startsAt = Carbon::parse($validated['starts_at']);
            $endsAt = Carbon::parse($validated['ends_at']);
            $minutes = $startsAt->diffInMinutes($endsAt);

            $priceRule = $this->findPriceRule(
                $validated['place_id'],
                $validated['zone_id'],
                $startsAt
            );

            $basePrice = (int) $resource->model->base_price_hour;
            $zoneCoef = (float) $resource->zone->price_coef;
            
            if ($priceRule) {
                $ruleKind = $priceRule->kind;
                $ruleValue = (float) $priceRule->value;
            } else {
                $ruleKind = 'coef';
                $ruleValue = 1.0;
            }

            if ($ruleKind === 'coef') {
                $hourPrice = $basePrice * $zoneCoef * $ruleValue;
            } else {
                $hourPrice = $ruleValue;
            }

            $amount = (int) round(($hourPrice / 60) * $minutes);

            // 3. Обновляем booking_resource
            if ($firstBookingResource) {
                $firstBookingResource->update([
                    'resource_id' => $validated['resource_id'],
                    'starts_at' => $validated['starts_at'],
                    'ends_at' => $validated['ends_at'],
                    'hour_price_snapshot' => $basePrice,
                    'rule_kind' => $ruleKind,
                    'rule_value' => $ruleValue,
                    'zone_coef_snapshot' => $zoneCoef,
                    'minutes' => $minutes,
                    'amount' => $amount,
                ]);

                // 4. Обновляем заказ если есть
                if ($booking->order) {
                    $booking->order->update([
                        'user_id' => $validated['user_id'],
                        'place_id' => $validated['place_id'],
                        'total_amount' => $amount,
                    ]);

                    // 5. Обновляем order_item
                    $orderItem = $booking->order->items()
                        ->where('type', 'table_time')
                        ->where('booking_resource_id', $firstBookingResource->id)
                        ->first();
                    
                    if ($orderItem) {
                        $orderItem->update([
                            'amount' => $amount,
                        ]);
                    }
                }
            }
        } else {
            // Если время/стол не изменились, просто обновляем user/place в заказе
            if ($booking->order) {
                $booking->order->update([
                    'user_id' => $validated['user_id'],
                    'place_id' => $validated['place_id'],
                ]);
            }
        }

        DB::commit();

        return redirect()->route('admin.bookings.show', $booking)
            ->with('success', 'Бронирование успешно обновлено!');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Booking update error: ' . $e->getMessage());
        
        return back()
            ->withInput()
            ->with('error', 'Ошибка обновления: ' . $e->getMessage());
    }
}
    
}
