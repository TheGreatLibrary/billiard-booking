<?php

namespace App\Livewire\Guest;

use Livewire\Component;
use App\Livewire\Traits\HasBookingFlow;

class BookingCreate extends Component
{
    use HasBookingFlow;

    public function mount()
    {
        $this->mountBookingFlow();
    }

    public function render()
    {
        return view('livewire.guest.booking-create')->layout('layouts.guest');
    }
}