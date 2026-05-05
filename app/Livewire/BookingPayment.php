<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Support\Facades\Auth;

class BookingPayment extends Component
{
    public Booking $booking;
    public $totalAmount = 0;

    public function mount(Booking $booking)
    {
        // Проверяем что бронирование принадлежит пользователю
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        // Проверяем что бронирование ожидает оплаты
        if ($booking->payment_status !== 'pending') {
            return redirect()->route('dashboard')->with('info', 'Это бронирование уже оплачено или отменено.');
        }

        $this->booking = $booking->load(['slots', 'place', 'equipment.productModel']);
        $this->totalAmount = $booking->total_amount;
    }

    public function payBooking($method)
    {
        try {
            $service = app(BookingService::class);
            $service->payBooking($this->booking, $method);
            session()->flash('success', 'Оплата прошла успешно! Бронирование подтверждено.');
            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function cancelBooking()
    {
        $this->booking->update([
            'status' => 'canceled',
            'payment_status' => 'canceled',
        ]);
        session()->flash('success', 'Бронирование отменено.');
        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.booking-payment')->layout('layouts.app');
    }
}