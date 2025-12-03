<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\BookingService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CleanupExpiredBookings
{
    public function __construct(private BookingService $bookingService) {}

    public function handle(Request $request, Closure $next)
    {
        if (!Cache::has('booking_cleanup_lock')) {
            Cache::put('booking_cleanup_lock', true, 60);
            
            $count = $this->bookingService->cleanupExpiredBookings();
            if ($count > 0) {
                Log::info("Отменено истекших бронирований: {$count}");
            }
        }

        return $next($request);
    }
}