<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Order;
use App\Services\BookingService;

class OrderPay extends Component
{
     public Order $order;
    public $paymentMethod = 'card';

    public function mount(Order $order)
    {
        if (!$order->canPay()) {
            session()->flash('error', 'Заказ уже оплачен или отменён');
            return redirect()->route('admin.orders.show', $order);
        }

        $this->order = $order->load(['user', 'items.productModel', 'booking']);
    }

    public function pay(BookingService $service)
    {
        $this->validate([
            'paymentMethod' => 'required|in:cash,card,online'
        ]);

        try {
            $service->payOrder($this->order, $this->paymentMethod);
            
            session()->flash('success', 'Заказ оплачен!');
            return redirect()->route('admin.orders.show', $this->order);
            
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.order-pay')
            ->layout('admin.layout.app-livewire')
            ->title('Оплата заказа #' . $this->order->id);
    }
}
