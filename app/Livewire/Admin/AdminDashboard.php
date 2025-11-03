<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\{Booking, Order, Payment, User, ProductType, ProductModel, Place, Zone, PriceRule, Resource};
use Illuminate\Support\Facades\DB;

class AdminDashboard extends Component
{
     public $total = [];
    public $monthly = [];

    public function mount()
    {
        $this->loadStatistics();
    }

    public function loadStatistics()
    {
        // Основные показатели
        $this->total = [
            'payments'      => Payment::count(),
            'amount'        => Payment::sum('amount') / 100,
            'orders'        => Order::count(),
            'bookings'      => Booking::count(),
            'users'         => User::count(),
            'productTypes'  => ProductType::count(),
            'productModels' => ProductModel::count(),
            'places'        => Place::count(),
            'zones'         => Zone::count(),
            'priceRules'    => PriceRule::count(),
            'resources'     => Resource::count(),
        ];

        // Разбивка по месяцам (SQLite)
        $this->monthly = Payment::select(
                DB::raw("strftime('%Y-%m', paid_at) as month"),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(amount) / 100 as amount')
            )
            ->whereNotNull('paid_at')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get()
            ->keyBy('month');
    }

    public function render()
    {
        return view('livewire.admin.admin-dashboard')
            ->layout('admin.layout.app-livewire');
    }
}
