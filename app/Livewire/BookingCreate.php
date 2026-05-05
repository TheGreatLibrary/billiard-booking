<?php

namespace App\Livewire;

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
        return view('livewire.User.stepper')->layout('layouts.app');
    }
}