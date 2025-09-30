<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Models\ProductType;
use App\Models\ProductModel;
use App\Models\Place;
use App\Models\Zone;
use App\Models\PriceRule;
use App\Models\Resource;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Основные показатели
        $total = [
            'payments'     => Payment::count(),
            'amount'       => Payment::sum('amount') / 100,
            'orders'       => Order::count(),
            'bookings'     => Booking::count(),
            'users'        => User::count(),
            'productTypes' => ProductType::count(),
            'productModels'=> ProductModel::count(),
            'places'       => Place::count(),
            'zones'        => Zone::count(),
            'priceRules'   => PriceRule::count(),
            'resources'    => Resource::count(),
        ];

        // Разбивка по месяцам (SQLite)
        $monthly = Payment::select(
                DB::raw("strftime('%Y-%m', paid_at) as month"),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(amount) / 100 as amount')
            )
            ->whereNotNull('paid_at')
            ->groupBy('month')
            ->orderBy('month','desc')
            ->limit(12)
            ->get()
            ->keyBy('month');

        return view('admin.dashboard', compact('total','monthly'));
    }
}
