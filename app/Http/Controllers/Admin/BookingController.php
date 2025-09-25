<?php
// app/Http/Controllers/Admin/BookingController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Models\Place;
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
        $places = Place::where('is_available', true)->get();
        
        return view('admin.bookings.create', compact('users', 'places'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'place_id' => 'required|exists:places,id',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
            'status' => 'required|in:pending,confirmed,canceled,completed',
            'notes' => 'nullable|string|max:500',
        ]);

        Booking::create($request->all());

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Бронирование создано успешно!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        $booking->load(['user', 'place', 'resources']);
        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Booking $booking)
    {
        $users = User::all();
        $places = Place::where('is_available', true)->get();
        
        return view('admin.bookings.edit', compact('booking', 'users', 'places'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Booking $booking)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'place_id' => 'required|exists:places,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'status' => 'required|in:pending,confirmed,canceled,completed',
            'notes' => 'nullable|string|max:500',
        ]);

        $booking->update($request->all());

        return redirect()->route('admin.bookings.show', $booking)
            ->with('success', 'Бронирование обновлено успешно!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        $booking->delete();

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Бронирование удалено успешно!');
    }

    /**
     * Change booking status
     */
    public function changeStatus(Booking $booking, Request $request)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,canceled,completed'
        ]);

        $booking->update(['status' => $request->status]);

        return back()->with('success', 'Статус бронирования изменен!');
    }
}