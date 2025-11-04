<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Order;
use App\Services\BookingService;

class OrderShow extends Component
{
    public Order $order;

    public function mount(Order $order)
    {
        $this->order = $order->load(['user', 'items.productModel', 'booking']);
    }

    public function deleteOrder()
    {
        if (!$this->order->canEdit()) {
            session()->flash('error', 'Нельзя удалить оплаченный заказ');
            return;
        }

        $this->order->booking->delete(); // Каскадно удалит Order
        session()->flash('success', 'Заказ удалён');
        return redirect()->route('admin.orders.index');
    }

    public function cancelOrder(BookingService $service)
    {
        try {
            $service->cancelOrder($this->order);
            $this->order->refresh();
            session()->flash('success', 'Заказ отменён. Средства будут возвращены.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.order-show')
            ->layout('admin.layout.app-livewire')
            ->title('Заказ #' . $this->order->id);
    }
}
