<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Payment;

class PaymentShow extends Component
{
     public Payment $payment;
    public $status;

    public function mount(Payment $payment)
    {
        $this->payment = $payment->load(['order.user', 'order.booking']);
        $this->status = $payment->status;
    }

    public function updatedStatus($value)
    {
        $this->validate([
            'status' => 'required|in:pending,paid,failed,refunded'
        ]);

        $this->payment->update([
            'status' => $value,
            'paid_at' => $value === 'paid' ? now() : $this->payment->paid_at,
        ]);
        
        $this->payment->refresh();
        session()->flash('success', 'Статус изменён!');
    }

    public function deletePayment()
    {
        if ($this->payment->status === 'paid') {
            session()->flash('error', 'Нельзя удалить оплаченный платёж');
            return;
        }
        
        $this->payment->delete();
        session()->flash('success', 'Платёж удалён');
        return redirect()->route('admin.payments.index');
    }

    public function render()
    {
        return view('livewire.admin.payment-show')
            ->layout('admin.layout.app-livewire')
            ->title('Платёж #' . $this->payment->id);
    }
}
