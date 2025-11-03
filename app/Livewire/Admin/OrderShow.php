<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Order;

class OrderShow extends Component
{
     public Order $order;

    public function mount(Order $order)
    {
        $this->order = $order->load(['user', 'items.productModel']);
    }

    public function deleteOrder()
    {
        $this->order->delete();
        session()->flash('success', 'Заказ удалён');
        return redirect()->route('admin.orders.index');
    }

    public function render()
    {
        return view('livewire.admin.order-show')
            ->layout('admin.layout.app-livewire')
            ->title('Заказ #' . $this->order->id);
    }
}
