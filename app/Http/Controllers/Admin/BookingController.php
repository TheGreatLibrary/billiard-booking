<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Place;
use App\Models\User;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['user', 'place'])->latest()->paginate(15);
        return view('admin.bookings.index', compact('bookings'));
    }

    public function create()
    {
        $users = User::all();
        $places = Place::all();
        return view('admin.bookings.create', compact('users', 'places'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'place_id' => 'required|exists:places,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'status' => 'required|in:pending,confirmed,canceled,completed',
        ]);

        Booking::create($request->all());

        return redirect()->route('admin.bookings.index')->with('success', 'Бронирование создано успешно');
    }

    public function show(Booking $booking)
    {
        $booking->load(['user', 'place', 'resources']);
        return view('admin.bookings.show', compact('booking'));
    }

    public function edit(Booking $booking)
    {
        $users = User::all();
        $places = Place::all();
        return view('admin.bookings.edit', compact('booking', 'users', 'places'));
    }

    public function update(Request $request, Booking $booking)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'place_id' => 'required|exists:places,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'status' => 'required|in:pending,confirmed,canceled,completed',
        ]);

        $booking->update($request->all());

        return redirect()->route('admin.bookings.index')->with('success', 'Бронирование обновлено успешно');
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();
        return redirect()->route('admin.bookings.index')->with('success', 'Бронирование удалено успешно');
    }

    public function changeStatus(Booking $booking, Request $request)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,canceled,completed'
        ]);

        $booking->update(['status' => $request->status]);

        return back()->with('success', 'Статус бронирования изменен');
    }
}