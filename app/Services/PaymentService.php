<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function createPayment(array $data): Payment
    {
        return Payment::create($data);
    }

    public function updatePayment(Payment $payment, array $data): Payment
    {
        $payment->update($data);
        return $payment;
    }

    public function deletePayment(Payment $payment): void
    {
        $payment->delete();
    }

    public function changeStatus(Payment $payment, string $status): Payment
    {
        $payment->update(['status' => $status]);
        return $payment;
    }

    public function getDashboardStats(): array
    {
        return [
            'total'         => Payment::count(),
            'completed'     => Payment::where('status', 'completed')->count(),
            'pending'       => Payment::where('status', 'pending')->count(),
            'failed'        => Payment::where('status', 'failed')->count(),
            'total_amount'  => Payment::where('status', 'completed')->sum('amount') ?? 0,
        ];
    }

    public function getFinancialStats(): array
    {
        $total = [
            'count'   => Payment::count(),
            'amount'  => Payment::sum('amount') / 100,
            'average' => Payment::count() > 0 ? Payment::avg('amount') / 100 : 0,
        ];

        $byStatus = Payment::select(
                'status',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(amount) as amount')
            )
            ->groupBy('status')
            ->get()
            ->mapWithKeys(fn($row) => [
                $row->status => [
                    'count'  => $row->count,
                    'amount' => $row->amount / 100,
                ]
            ]);

        $monthly = Payment::select(
                DB::raw("strftime('%Y-%m', paid_at) as month"),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(amount) as amount')
            )
            ->whereNotNull('paid_at')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get()
            ->mapWithKeys(fn($row) => [
                $row->month => [
                    'count'  => $row->count,
                    'amount' => $row->amount / 100,
                ]
            ]);

        return compact('total', 'byStatus', 'monthly');
    }
}
