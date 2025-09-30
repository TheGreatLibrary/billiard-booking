<?php
// app/Http/Controllers/Admin/PaymentController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payments = Payment::with(['user', 'order'])
            ->latest()
            ->paginate(15);

        // Статистика платежей
        $stats = [
            'total' => Payment::count(),
            'completed' => Payment::where('status', 'completed')->count(),
            'pending' => Payment::where('status', 'pending')->count(),
            'failed' => Payment::where('status', 'failed')->count(),
            'total_amount' => Payment::where('status', 'completed')->sum('amount') ?? 0,
        ];

        return view('admin.payments.index', compact('payments', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();
        $orders = Order::whereDoesntHave('payments', function($query) {
            $query->where('status', 'completed');
        })->get();

        return view('admin.payments.create', compact('users', 'orders'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'order_id' => 'nullable|exists:orders,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,online,transfer',
            'status' => 'required|in:pending,completed,failed,refunded',
            'transaction_id' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        Payment::create($request->all());

        return redirect()->route('admin.payments.index')
            ->with('success', 'Платеж создан успешно!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        $payment->load(['user', 'order']);
        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        $users = User::all();
        $orders = Order::all();
        
        return view('admin.payments.edit', compact('payment', 'users', 'orders'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'order_id' => 'nullable|exists:orders,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,online,transfer',
            'status' => 'required|in:pending,completed,failed,refunded',
            'transaction_id' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        $payment->update($request->all());

        return redirect()->route('admin.payments.show', $payment)
            ->with('success', 'Платеж обновлен успешно!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        $payment->delete();

        return redirect()->route('admin.payments.index')
            ->with('success', 'Платеж удален успешно!');
    }

    /**
     * Change payment status
     */
    public function changeStatus(Payment $payment, Request $request)
    {
        $request->validate([
            'status' => 'required|in:pending,completed,failed,refunded'
        ]);

        $payment->update(['status' => $request->status]);

        return back()->with('success', 'Статус платежа изменен!');
    }

    /**
     * Financial statistics
     */
    public function statistics()
    {
        // === Общие цифры ===
        $total = [
            'count'   => Payment::count(),
            'amount'  => Payment::sum('amount') / 100, // в рублях
            'average' => Payment::count() > 0
                ? Payment::avg('amount') / 100
                : 0,
        ];

        // === Разбивка по статусам ===
        $byStatusRaw = Payment::select(
                'status',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(amount) as amount')
            )
            ->groupBy('status')
            ->get();

        $byStatus = $byStatusRaw->mapWithKeys(function ($row) {
            return [
                $row->status => [
                    'count'  => $row->count,
                    'amount' => $row->amount / 100,
                ]
            ];
        });

        // === Разбивка по месяцам === (SQLite -> strftime)
        $monthlyRaw = Payment::select(
                DB::raw("strftime('%Y-%m', paid_at) as month"),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(amount) as amount')
            )
            ->whereNotNull('paid_at')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        $monthly = $monthlyRaw->mapWithKeys(function ($row) {
            return [
                $row->month => [
                    'count'  => $row->count,
                    'amount' => $row->amount / 100,
                ]
            ];
        });

        return view('admin.payments.statistics', compact('total','byStatus','monthly'));
    }
}
