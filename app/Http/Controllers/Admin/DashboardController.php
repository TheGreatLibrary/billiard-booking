<?php
// app/Http\Controllers\Admin\DashboardController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Booking;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Place;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Основная статистика
        $stats = [
            'total_users' => User::count(),
            'new_users_today' => User::whereDate('created_at', today())->count(),
            'total_bookings' => Booking::count(),
            'active_bookings' => Booking::where('status', 'confirmed')->count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'total_orders' => Order::count(),
            'total_revenue' => Payment::where('status', 'completed')->sum('amount') ?? 0,
            'today_revenue' => Payment::whereDate('created_at', today())->where('status', 'completed')->sum('amount') ?? 0,
            'total_places' => Place::count(),
            'available_places' => Place::where('is_available', true)->count(),
        ];

        // Последние бронирования
        $recentBookings = Booking::with(['user', 'place'])
            ->latest()
            ->take(8)
            ->get();

        // Последние пользователи
        $recentUsers = User::latest()
            ->take(6)
            ->get();

        // Статистика по дням (последние 7 дней)
        $weeklyStats = $this->getWeeklyStats();

        return view('admin.dashboard', compact('stats', 'recentBookings', 'recentUsers', 'weeklyStats'));
    }

    private function getWeeklyStats()
    {
        $stats = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $stats[] = [
                'date' => $date->format('d.m'),
                'bookings' => Booking::whereDate('created_at', $date)->count(),
                'users' => User::whereDate('created_at', $date)->count(),
                'revenue' => Payment::whereDate('created_at', $date)->where('status', 'completed')->sum('amount') ?? 0,
            ];
        }
        return $stats;
    }

    public function stats(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30));
        $endDate = $request->get('end_date', now());

        $stats = [
            'users' => [
                'total' => User::count(),
                'new' => User::whereBetween('created_at', [$startDate, $endDate])->count(),
            ],
            'bookings' => [
                'total' => Booking::count(),
                'confirmed' => Booking::where('status', 'confirmed')->count(),
                'pending' => Booking::where('status', 'pending')->count(),
            ],
            'revenue' => [
                'total' => Payment::where('status', 'completed')->sum('amount') ?? 0,
                'period' => Payment::whereBetween('created_at', [$startDate, $endDate])
                    ->where('status', 'completed')
                    ->sum('amount') ?? 0,
            ],
        ];

        return response()->json($stats);
    }
}