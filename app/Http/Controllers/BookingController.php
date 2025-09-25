<?php
// app/Http/Controllers/BookingController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Place;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bookings = Booking::with(['user', 'place'])
            ->where('user_id', auth()->id()) // Только бронирования текущего пользователя
            ->orderBy('start_time', 'desc')
            ->get();

        return view('bookings.index', compact('bookings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $places = Place::where('is_available', true)->get();
        return view('bookings.create', compact('places'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'place_id' => 'required|exists:places,id',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            Booking::create([
                'user_id' => auth()->id(),
                'place_id' => $request->place_id,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'notes' => $request->notes,
                'status' => 'pending',
            ]);

            return redirect()->route('bookings.index')
                ->with('success', 'Бронирование создано успешно!');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка при создании бронирования: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        // Проверяем, что пользователь может просматривать это бронирование
        if ($booking->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $booking->load(['user', 'place', 'resources']);
        return view('bookings.show', compact('booking'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Booking $booking)
    {
        // Проверяем права на редактирование
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        $places = Place::where('is_available', true)->get();
        return view('bookings.edit', compact('booking', 'places'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Booking $booking)
    {
        // Проверяем права на редактирование
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'place_id' => 'required|exists:places,id',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
            'notes' => 'nullable|string|max:500',
        ]);

        $booking->update($request->all());

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Бронирование обновлено успешно!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        // Проверяем права на удаление
        if ($booking->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $booking->delete();

        return redirect()->route('bookings.index')
            ->with('success', 'Бронирование удалено успешно!');
    }
}