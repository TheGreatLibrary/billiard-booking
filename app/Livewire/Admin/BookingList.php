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
        // ✅ ИСПРАВЛЕНО: Добавлен eager loading для resource.productModel
        $bookings = Booking::with([
                'user', 
                'place', 
                'resource.productModel', // ✅ Загружаем связь productModel
                'slots'
            ])
            ->when($this->search, fn($q) => 
                $q->where(function($query) {
                    $query->whereHas('user', fn($qq) => 
                        $qq->where('name', 'like', "%{$this->search}%")
                           ->orWhere('email', 'like', "%{$this->search}%")
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
        
        // ✅ Проверяем через метод isPaid() если он есть
        if (method_exists($booking, 'isPaid') && $booking->isPaid()) {
            session()->flash('error', 'Нельзя удалить оплаченное бронирование');
            return;
        }
        
        // Альтернативная проверка если метода нет
        if ($booking->payment_status === 'paid') {
            session()->flash('error', 'Нельзя удалить оплаченное бронирование');
            return;
        }

        $booking->delete();
        session()->flash('success', "Бронирование #{$bookingId} удалено");
    }
    
    /**
     * ✅ Сброс фильтров
     */
    public function resetFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->paymentStatusFilter = '';
        $this->resetPage();
    }
}