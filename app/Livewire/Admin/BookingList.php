<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Booking;

class BookingList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';

    public function render()
    {
        $bookings = Booking::with(['user', 'place'])
            ->when($this->search, fn($q) => 
                $q->whereHas('user', fn($qq) => 
                    $qq->where('name', 'like', "%{$this->search}%")
                )
            )
            ->when($this->statusFilter, fn($q) => 
                $q->where('status', $this->statusFilter)
            )
            ->latest()
            ->paginate(15);

        return view('livewire.admin.booking-list', compact('bookings'))->layout('admin.layout.app-livewire');
    }

    public function deleteBooking($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);
        
        if (in_array($booking->status, ['confirmed', 'finished'])) {
            session()->flash('error', 'Нельзя удалить подтверждённое бронирование');
            return;
        }

        $booking->delete();
        session()->flash('success', "Бронирование #{$bookingId} удалено");
    }
}
