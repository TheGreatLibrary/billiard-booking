<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Booking;

class BookingShow extends Component
{
    public Booking $booking;

    public function mount(Booking $booking)
    {
        // ✅ ИСПРАВЛЕНО: resource.model → resource.productModel
        $this->booking = $booking->load([
            'user',
            'place',
            'resource.productModel', // ✅ Исправлено
            'resource.zone',
            'slots',         
            'equipment.productModel', // ✅ Добавлено для оборудования
        ]);
    }

    public function deleteBooking()
    {
        if (in_array($this->booking->status, ['confirmed', 'finished'])) {
            session()->flash('error', 'Нельзя удалить подтверждённое или завершённое бронирование');
            return;
        }

        $bookingId = $this->booking->id;
        $this->booking->delete();

        session()->flash('success', "Бронирование #{$bookingId} удалено");
        return redirect()->route('admin.bookings.index');
    }

    public function render()
    {
        return view('livewire.admin.booking-show')
            ->layout('admin.layout.app-livewire');
    }
}