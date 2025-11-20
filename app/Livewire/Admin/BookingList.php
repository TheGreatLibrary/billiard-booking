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
    public $paymentStatusFilter = '';

    public function render()
    {
        $bookings = Booking::with(['user', 'place', 'resource'])
            ->when($this->search, fn($q) => 
                $q->where(function($query) {
                    $query->whereHas('user', fn($qq) => 
                        $qq->where('name', 'like', "%{$this->search}%")
                    )
                    ->orWhere('guest_name', 'like', "%{$this->search}%")
                    ->orWhere('guest_email', 'like', "%{$this->search}%");
                })
            )
            ->when($this->statusFilter, fn($q) => 
                $q->where('status', $this->statusFilter)
            )
            ->when($this->paymentStatusFilter, fn($q) => 
                $q->where('payment_status', $this->paymentStatusFilter)
            )
            ->latest('created_at')
            ->paginate(15);

        return view('livewire.admin.booking-list', compact('bookings'))
            ->layout('admin.layout.app-livewire');
    }

    public function deleteBooking($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);
        
        if ($booking->isPaid()) {
            session()->flash('error', 'Нельзя удалить оплаченное бронирование');
            return;
        }

        $booking->delete();
        session()->flash('success', "Бронирование #{$bookingId} удалено");
    }
}
