<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;

class BookingController extends Controller
{
    /**
     * Список бронирований пользователя
     */
    public function index()
    {
        $user = auth()->user();

        // Текущие и будущие бронирования
        $upcomingBookings = Booking::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('starts_at', '>=', now())
            ->with(['place', 'bookingResources.resource', 'order'])
            ->orderBy('starts_at', 'asc')
            ->get();

        // Прошлые бронирования
        $pastBookings = Booking::where('user_id', $user->id)
            ->where(function($q) {
                $q->whereIn('status', ['finished', 'canceled', 'no_show'])
                  ->orWhere('ends_at', '<', now());
            })
            ->with(['place', 'bookingResources.resource', 'order'])
            ->orderBy('starts_at', 'desc')
            ->limit(10)
            ->get();

        return view('user.bookings.index', compact('upcomingBookings', 'pastBookings'));
    }

    /**
     * Детали конкретного бронирования
     */
    public function show(Booking $booking)
    {
        // Проверяем, что это бронирование принадлежит пользователю
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Это не ваше бронирование');
        }

        $booking->load([
            'place',
            'bookingResources.resource.model',
            'bookingResources.resource.zone',
            'order.items.bookingResource',
            'order.items.productModel',
        ]);

        return view('user.bookings.show', compact('booking'));
    }

    /**
     * Отменить бронирование
     */
    public function cancel(Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        if ($booking->status === 'canceled') {
            return back()->with('error', 'Бронирование уже отменено');
        }

        if (in_array($booking->status, ['finished', 'no_show'])) {
            return back()->with('error', 'Нельзя отменить завершённое бронирование');
        }

        $booking->update(['status' => 'canceled']);

        return back()->with('success', 'Бронирование отменено');
    }
}