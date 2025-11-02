<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\OrderService;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of the resource.
     */
public function index()
{
    $orders = Order::with(['user', 'items'])->latest()->paginate(15);

    // Статусы
    $statusCounts = [
        'completed'  => Order::where('status', 'completed')->count(),
        'processing' => Order::where('status', 'processing')->count(),
        'pending'    => Order::where('status', 'pending')->count(),
        'canceled'   => Order::where('status', 'canceled')->count(),
    ];

    return view('admin.orders.index', compact('orders', 'statusCounts'));
}


    /**
     * Show the form for creating a new resource.
     */
public function create()
{
    $bookings = \App\Models\Booking::with(['place', 'user'])->get();
    $users = \App\Models\User::all();

    return view('admin.orders.create', compact('bookings', 'users'));
}

public function store(Request $request)
{
    $validated = $request->validate([
        'booking_id'    => 'required|exists:bookings,id',
        'user_id'       => 'required|exists:users,id',
        'total_amount'  => 'required|numeric|min:0',
        'status'        => 'required|in:pending,processing,completed,canceled',
        'notes'         => 'nullable|string|max:500',
    ]);

    $order = $this->orderService->createOrder($validated);

    return redirect()->route('admin.orders.show', $order)
        ->with('success', 'Заказ создан успешно!');
}


    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $order->load(['user', 'items', 'payments', 'booking.place']);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        $users = User::all();
        return view('admin.orders.edit', compact('order', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'user_id'      => 'required|exists:users,id',
            'total_amount' => 'required|numeric|min:0',
            'status'       => 'required|in:pending,processing,completed,canceled',
            'notes'        => 'nullable|string|max:500',
        ]);

        $order->update([
            ...$validated,
            'total_amount' => $validated['total_amount'],
        ]);

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Заказ обновлён успешно!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', 'Заказ удалён успешно!');
    }

public function changeStatus(Order $order, Request $request)
{
    $request->validate([
        'status' => 'required|in:pending,processing,completed,canceled',
    ]);

    $order->update([
        'status' => $request->status,
    ]);

    // Перенаправляем обратно на страницу заказа
    return redirect()->route('admin.orders.show', $order)
                     ->with('success', 'Статус заказа обновлён!');
}

}
