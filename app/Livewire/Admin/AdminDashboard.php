<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\{Booking, User, ProductType, ProductModel, Place, Zone, PriceRule, Resource};
use Illuminate\Support\Facades\DB;

class AdminDashboard extends Component
{
    public $total = [];
    public $monthly = [];
    public $statusStats = [];
    public $paymentStatusStats = [];

    public function mount()
    {
        $this->loadStatistics();
    }

    public function loadStatistics()
    {
        // Основные показатели
        $this->total = [
            'bookings'      => Booking::count(),
            'bookings_paid' => Booking::where('payment_status', 'paid')->count(),
            'bookings_pending' => Booking::where('payment_status', 'pending')->count(),
            'amount'        => Booking::where('payment_status', 'paid')->sum('total_amount') / 100, // конвертируем в рубли
            'users'         => User::count(),
            'productTypes'  => ProductType::count(),
            'productModels' => ProductModel::count(),
            'places'        => Place::count(),
            'zones'         => Zone::count(),
            'priceRules'    => PriceRule::count(),
            'resources'     => Resource::count(),
        ];

        // Статистика по статусам бронирований
        $this->statusStats = Booking::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->keyBy('status')
            ->map(fn($item) => $item->count);

        // Статистика по статусам оплаты
        $this->paymentStatusStats = Booking::select('payment_status', DB::raw('COUNT(*) as count'))
            ->groupBy('payment_status')
            ->get()
            ->keyBy('payment_status')
            ->map(fn($item) => $item->count);

        // Разбивка по месяцам (SQLite) - оплаченные бронирования
        $this->monthly = Booking::select(
                DB::raw("strftime('%Y-%m', paid_at) as month"),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total_amount) / 100.0 as amount')
            )
            ->where('payment_status', 'paid')
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