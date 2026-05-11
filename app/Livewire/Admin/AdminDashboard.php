<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\{Booking, User, ProductType, ProductModel, Place, Zone, PriceRule, Resource, BookingSlot};
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboard extends Component
{
    public $total = [];
    public $monthly = [];
    public $statusStats = [];
    public $paymentStatusStats = [];
    public $revenue = [];
    public $popularTables = [];
    public $weekdayStats = [];
    public $recentBookings = [];
    public $chartData = [];

    public function mount()
    {
        $this->loadStatistics();
    }

    public function loadStatistics()
    {
        $now = Carbon::now();

        // Основные показатели
        $totalPaid = Booking::where('payment_status', 'paid');
        $this->total = [
            'bookings'         => Booking::count(),
            'bookings_paid'    => (clone $totalPaid)->count(),
            'bookings_pending' => Booking::where('payment_status', 'pending')->count(),
            'amount'           => (clone $totalPaid)->sum('total_amount'),
            'users'            => User::count(),
            'productTypes'     => ProductType::count(),
            'productModels'    => ProductModel::count(),
            'places'           => Place::count(),
            'zones'            => Zone::count(),
            'priceRules'       => PriceRule::count(),
            'resources'        => Resource::count(),
            'tables'           => Resource::where('type', 'table')->count(),
            'equipment'        => Resource::where('type', 'equipment')->count(),
        ];

        // Выручка: сегодня / неделя / месяц / средний чек
        $this->revenue = [
            'today' => Booking::where('payment_status', 'paid')
                ->whereDate('paid_at', $now->toDateString())
                ->sum('total_amount'),
            'week' => Booking::where('payment_status', 'paid')
                ->where('paid_at', '>=', $now->copy()->startOfWeek())
                ->sum('total_amount'),
            'month' => Booking::where('payment_status', 'paid')
                ->where('paid_at', '>=', $now->copy()->startOfMonth())
                ->sum('total_amount'),
            'avg_check' => Booking::where('payment_status', 'paid')
                ->avg('total_amount') ?? 0,
            'today_count' => Booking::where('payment_status', 'paid')
                ->whereDate('paid_at', $now->toDateString())
                ->count(),
        ];

        // Статусы бронирований
        $this->statusStats = Booking::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')->get()->keyBy('status')->map(fn($i) => $i->count);

        // Статусы оплаты
        $this->paymentStatusStats = Booking::select('payment_status', DB::raw('COUNT(*) as count'))
            ->groupBy('payment_status')->get()->keyBy('payment_status')->map(fn($i) => $i->count);

        // Популярные столы (топ-5 по количеству бронирований)
        $this->popularTables = BookingSlot::select('resource_id', DB::raw('COUNT(DISTINCT booking_id) as bookings_count'))
            ->groupBy('resource_id')
            ->orderByDesc('bookings_count')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $resource = Resource::with('productModel')->find($item->resource_id);
                return [
                    'code' => $resource?->code ?? '—',
                    'model' => $resource?->productModel?->name ?? '—',
                    'count' => $item->bookings_count,
                ];
            });

        // Загруженность по дням недели
        $this->weekdayStats = BookingSlot::select(
                DB::raw("CAST(strftime('%w', slot_date) AS INTEGER) as weekday"),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('weekday')
            ->orderBy('weekday')
            ->get()
            ->mapWithKeys(function ($item) {
                $days = ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'];
                return [$days[$item->weekday] ?? '?' => $item->count];
            });

        // Последние 5 бронирований
        $this->recentBookings = Booking::with(['user', 'place'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Данные для графика (последние 6 месяцев)
        $this->monthly = Booking::select(
                DB::raw("strftime('%Y-%m', paid_at) as month"),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total_amount) as amount')
            )
            ->where('payment_status', 'paid')
            ->whereNotNull('paid_at')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(6)
            ->get()
            ->keyBy('month');

        // Данные для Chart.js
        $reversed = $this->monthly->sortKeys();
        $this->chartData = [
            'labels' => $reversed->keys()->map(function ($m) {
                $months = ['01'=>'Янв','02'=>'Фев','03'=>'Мар','04'=>'Апр','05'=>'Май','06'=>'Июн',
                           '07'=>'Июл','08'=>'Авг','09'=>'Сен','10'=>'Окт','11'=>'Ноя','12'=>'Дек'];
                $parts = explode('-', $m);
                return ($months[$parts[1]] ?? $parts[1]) . ' ' . $parts[0];
            })->values()->toArray(),
            'amounts' => $reversed->pluck('amount')->values()->toArray(),
            'counts' => $reversed->pluck('count')->values()->toArray(),
        ];
    }

    public function render()
    {
        return view('livewire.admin.admin-dashboard')
            ->layout('admin.layout.app-livewire')
            ->title('Панель управления');
    }
}