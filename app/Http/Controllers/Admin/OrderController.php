<?php
// app/Http/Controllers/Admin/OrderController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with(['user', 'items'])
            ->latest()
            ->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();
        return view('admin.orders.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|in:pending,processing,completed,canceled',
            'notes' => 'nullable|string|max:500',
        ]);

        Order::create($request->all());

        return redirect()->route('admin.orders.index')
            ->with('success', 'Заказ создан успешно!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $order->load(['user', 'items', 'payments']);
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
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|in:pending,processing,completed,canceled',
            'notes' => 'nullable|string|max:500',
        ]);

        $order->update($request->all());

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Заказ обновлен успешно!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', 'Заказ удален успешно!');
    }

    /**
     * Change order status
     */
    public function changeStatus(Order $order, Request $request)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,canceled'
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('success', 'Статус заказа изменен!');
    }
}