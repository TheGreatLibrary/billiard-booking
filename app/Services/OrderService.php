<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class OrderService
{
    public function listOrders(int $perPage = 15): LengthAwarePaginator
    {
        return Order::with(['user', 'items'])
            ->latest()
            ->paginate($perPage);
    }

    public function createOrder(array $data): Order
    {
        $booking = Booking::findOrFail($data['booking_id']);

        return Order::create([
            'booking_id'   => $booking->id,
            'user_id'      => $data['user_id'],
            'place_id'     => $booking->place_id,
            'total_amount' => (int)($data['total_amount'] * 100),
            'status'       => $data['status'],
            'notes'        => $data['notes'] ?? null,
        ]);
    }

    public function updateOrder(Order $order, array $data): bool
    {
        return $order->update($data);
    }

    public function deleteOrder(Order $order): bool
    {
        return $order->delete();
    }

    public function getUsers(): Collection
    {
        return \App\Models\User::all();
    }

    public function changeStatus(Order $order, string $status): bool
    {
        return $order->update(['status' => $status]);
    }

    public function getOrderWithRelations(Order $order): Order
    {
        return $order->load(['user', 'items', 'payments']);
    }
}
