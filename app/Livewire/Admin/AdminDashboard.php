<?php

namespace App\Livewire\Admin;
use Livewire\Component;
use App\Models\{Booking, Order, User, ProductType, ProductModel, Place, Zone, PriceRule, Resource};
use Illuminate\Support\Facades\DB;

class AdminDashboard extends Component
{
    public $total = [];
    public $monthly = [];
    public $statusStats = [];

    public function mount()
    {
        $this->loadStatistics();
    }

    public function loadStatistics()
    {
        // Основные показатели
        $this->total = [
            'orders'        => Booking::count(),
            'orders_paid'   => Booking::where('status', 'paid')->count(),
            'orders_pending'=> Booking::where('status', 'pending')->count(),
            'amount'        => Booking::where('status', 'paid')->sum('total_amount'),
            'users'         => User::count(),
            'productTypes'  => ProductType::count(),
            'productModels' => ProductModel::count(),
            'places'        => Place::count(),
            'zones'         => Zone::count(),
            'priceRules'    => PriceRule::count(),
            'resources'     => Resource::count(),
        ];

        // Статистика по статусам заказов
        $this->statusStats = Booking::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->keyBy('status')
            ->map(fn($item) => $item->count);

        // Разбивка по месяцам (SQLite) - оплаченные заказы
        $this->monthly = Booking::select(
                DB::raw("strftime('%Y-%m', paid_at) as month"),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total_amount) as amount')
            )
            ->where('status', 'paid')
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
            ->layout('admin.layout.app-livewire')
            ->title('Панель управления');
    }
}