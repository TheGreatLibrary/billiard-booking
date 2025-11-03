<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Payment;

class PaymentList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $methodFilter = '';
    public $sortBy = 'paid_at';
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
        $payments = Payment::with(['order.user', 'order.booking'])
            ->when($this->search, fn($q) => 
                $q->whereHas('order.user', fn($qq) => 
                    $qq->where('name', 'like', "%{$this->search}%")
                      ->orWhere('email', 'like', "%{$this->search}%")
                )
            )
            ->when($this->statusFilter, fn($q) => 
                $q->where('status', $this->statusFilter)
            )
            ->when($this->methodFilter, fn($q) => 
                $q->where('method', $this->methodFilter)
            )
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(15);

        // Статистика
        $stats = [
            'total' => Payment::count(),
            'paid' => Payment::where('status', 'paid')->count(),
            'pending' => Payment::where('status', 'pending')->count(),
            'failed' => Payment::where('status', 'failed')->count(),
            'total_amount' => Payment::where('status', 'paid')->sum('amount'),
        ];

        return view('livewire.admin.payment-list', compact('payments', 'stats'))
            ->layout('admin.layout.app-livewire')
            ->title('Платежи');
    }

    public function deletePayment($paymentId)
    {
        $payment = Payment::findOrFail($paymentId);
        
        if ($payment->status === 'paid') {
            session()->flash('error', 'Нельзя удалить оплаченный платёж');
            return;
        }
        
        $payment->delete();
        session()->flash('success', 'Платёж удалён');
    }
}
