<?php

namespace App\Livewire\Admin;

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
        return view('livewire.admin.booking-create')->layout('admin.layout.app-livewire');
    }
}