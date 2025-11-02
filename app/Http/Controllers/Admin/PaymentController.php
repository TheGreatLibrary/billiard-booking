<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PaymentRequest;
use App\Models\Payment;
use App\Models\Order;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index()
    {
        $payments = Payment::with(['user', 'order'])->latest()->paginate(15);
        $stats = $this->paymentService->getDashboardStats();

        return view('admin.payments.index', compact('payments', 'stats'));
    }

    public function create()
    {
        $users = User::all();
        $orders = Order::whereDoesntHave('payments', fn($q) => $q->where('status', 'completed'))->get();

        return view('admin.payments.create', compact('users', 'orders'));
    }

    public function store(PaymentRequest $request)
    {
        $this->paymentService->createPayment($request->validated());

        return redirect()
            ->route('admin.payments.index')
            ->with('success', 'Платеж создан успешно!');
    }

    public function update(PaymentRequest $request, Payment $payment)
    {
        $this->paymentService->updatePayment($payment, $request->validated());

        return redirect()
            ->route('admin.payments.show', $payment)
            ->with('success', 'Платеж обновлен успешно!');
    }

    public function destroy(Payment $payment)
    {
        $this->paymentService->deletePayment($payment);

        return redirect()
            ->route('admin.payments.index')
            ->with('success', 'Платеж удален успешно!');
    }

    public function changeStatus(Payment $payment, Request $request)
    {
        $validated = $request->validate(['status' => 'required|in:pending,completed,failed,refunded']);
        $this->paymentService->changeStatus($payment, $validated['status']);

        return back()->with('success', 'Статус платежа изменен!');
    }

    public function statistics()
    {
        $stats = $this->paymentService->getFinancialStats();

        return view('admin.payments.statistics', $stats);
    }
}
