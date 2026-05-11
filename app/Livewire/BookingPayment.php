<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Booking;
use App\Services\BookingService;
use App\Mail\BookingCancelled;
use Illuminate\Support\Facades\{Auth, Mail};

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

        $email = $this->booking->guest_email ?? $this->booking->user?->email;
        if ($email) {
            try {
                $this->booking->loadMissing(['slots', 'place', 'user']);
                Mail::to($email)->send(new BookingCancelled($this->booking, 'canceled'));
            } catch (\Exception $e) {
                \Log::warning("Cancel email failed for booking #{$this->booking->id}: " . $e->getMessage());
            }
        }

        session()->flash('success', 'Бронирование отменено.');
        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.booking-payment')->layout('layouts.app');
    }
}