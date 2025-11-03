<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;

class OrderList extends Component
{
     use WithPagination;

    public $search = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortByColumn($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        $orders = Order::with(['user', 'booking', 'items'])
            ->when($this->search, fn($q) => 
                $q->whereHas('user', fn($qq) => 
                    $qq->where('name', 'like', "%{$this->search}%")
                      ->orWhere('email', 'like', "%{$this->search}%")
                )
            )
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(15);

        $stats = [
            'total' => Order::count(),
            'total_amount' => Order::sum('total_amount'),
        ];

        return view('livewire.admin.order-list', compact('orders', 'stats'))
            ->layout('admin.layout.app-livewire')
            ->title('Заказы');
    }

    public function deleteOrder($orderId)
    {
        $order = Order::findOrFail($orderId);
        
        // Проверка: можно ли удалить
        if ($order->payments()->exists()) {
            session()->flash('error', 'Нельзя удалить заказ с платежами');
            return;
        }
        
        $order->delete();
        session()->flash('success', 'Заказ удалён');
    }
}
