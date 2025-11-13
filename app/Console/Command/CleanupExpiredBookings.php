<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BookingService;

class CleanupExpiredBookings extends Command
{
    protected $signature = 'bookings:cleanup-expired';
    protected $description = 'Отменить бронирования со статусом pending, у которых истек expires_at';

    public function handle(BookingService $service)
    {
        $this->info('Запуск очистки истекших бронирований...');
        
        $count = $service->cleanupExpiredBookings();
        
        $this->info("Отменено бронирований: {$count}");
        
        return 0;
    }
}