<?php
// app/Http\Controllers\Admin\BookingController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Models\Place;
use App\Models\Zone;
use App\Models\Resource;
use Illuminate\Http\Request;

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
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'place_id' => 'required|exists:places,id',
            'zone_id' => 'required|exists:zones,id',
            'resource_id' => 'required|exists:resources,id',
            'starts_at' => 'required|date|after:now', // исправлено на starts_at
            'ends_at' => 'required|date|after:starts_at',
            'status' => 'required|in:pending,confirmed,canceled,completed',
            'notes' => 'nullable|string|max:500',
        ]);

        Booking::create($request->all());

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Бронирование создано успешно!');
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

    
}
