<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Booking;
use App\Services\BookingService;

class BookingPay extends Component
{
    public Booking $booking;
    public $paymentMethod = 'card';

    public function mount(Booking $booking)
    {
        if (!$booking->canPay()) {
            session()->flash('error', 'Бронирование уже оплачено или отменено');
            return redirect()->route('admin.bookings.show', $booking);
        }

        $this->booking = $booking->load([
            'user', 
            'place', 
            'resource.model',
            'slots',
            'equipment.productModel'
        ]);
    }

    public function pay(BookingService $service)
    {
        $this->validate([
            'paymentMethod' => 'required|in:cash,card,online'
        ]);

        try {
            $service->payBooking($this->booking, $this->paymentMethod);
            
            session()->flash('success', 'Бронирование оплачено!');
            return redirect()->route('admin.bookings.show', $this->booking);
            
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.booking-pay')
            ->layout('admin.layout.app-livewire')
            ->title('Оплата бронирования #' . $this->booking->id);
    }
}