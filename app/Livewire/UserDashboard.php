<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class UserDashboard extends Component
{
    public $stats = [];
    public $recentBookings = [];

    public function mount()
    {
        $this->loadStats();
        $this->loadRecentBookings();
    }

    private function loadStats()
    {
        $userId = Auth::id();

        $this->stats = [
            'total' => Booking::where('user_id', $userId)->count(),
            'active' => Booking::where('user_id', $userId)
                ->whereIn('payment_status', ['pending', 'paid'])
                ->whereIn('status', ['pending', 'confirmed'])
                ->count(),
            'completed' => Booking::where('user_id', $userId)
                ->where('status', 'finished')
                ->count(),
            'total_spent' => Booking::where('user_id', $userId)
                ->where('payment_status', 'paid')
                ->sum('total_amount') / 100,
        ];
    }

    private function loadRecentBookings()
    {
        $this->recentBookings = Booking::where('user_id', Auth::id())
            ->with([
                'place',
                'resource.model',
                'slots',
                'equipment.productModel'
            ])
            ->latest('created_at')
            ->limit(10)
            ->get();
    }

    public function cancelBooking($bookingId)
    {
        $booking = Booking::where('id', $bookingId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$booking) {
            session()->flash('error', 'Бронирование не найдено');
            return;
        }

        if ($booking->payment_status === 'paid') {
            session()->flash('error', 'Нельзя отменить оплаченное бронирование. Обратитесь к администратору для возврата средств.');
            return;
        }

        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            session()->flash('error', 'Это бронирование уже нельзя отменить');
            return;
        }

        $booking->update([
            'status' => 'canceled',
            'payment_status' => 'canceled',
        ]);

        session()->flash('success', 'Бронирование отменено');
        
        $this->loadStats();
        $this->loadRecentBookings();
    }

    public function render()
    {
        return view('livewire.User.user-dashboard')
            ->layout('layouts.app')
            ->title('Личный кабинет');
    }
}