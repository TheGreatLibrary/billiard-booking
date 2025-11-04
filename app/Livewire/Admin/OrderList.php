<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;

class OrderList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = ''; // ← Добавили
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter() // ← Добавили
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
            ->when($this->statusFilter, fn($q) =>  // ← Добавили
                $q->where('status', $this->statusFilter)
            )
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(15);

        $stats = [
            'total' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(), // ← Добавили
            'paid' => Order::where('status', 'paid')->count(), // ← Добавили
            'total_amount' => Order::where('status', 'paid')->sum('total_amount'), // ← Изменили
        ];

        return view('livewire.admin.order-list', compact('orders', 'stats'))
            ->layout('admin.layout.app-livewire')
            ->title('Заказы');
    }

    public function deleteOrder($orderId)
    {
        $order = Order::findOrFail($orderId);
        
        if (!$order->canEdit()) { // ← Изменили
            session()->flash('error', 'Нельзя удалить оплаченный заказ');
            return;
        }
        
        $order->booking->delete(); // ← Каскадно удалит Order
        session()->flash('success', 'Заказ удалён');
    }
}