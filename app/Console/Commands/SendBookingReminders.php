<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Mail\BookingReminder;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendBookingReminders extends Command
{
    protected $signature = 'bookings:send-reminders';
    protected $description = 'Отправить напоминания за 1 час до начала бронирования';

    public function handle()
    {
        $this->info('Проверяю бронирования для напоминаний...');

        // Ищем подтверждённые бронирования, у которых первый слот через ~1 час
        $now = Carbon::now();
        $targetTime = $now->copy()->addHour();

        // Бронирования с оплатой, у которых ещё не отправлено напоминание
        $bookings = Booking::where('payment_status', 'paid')
            ->where('status', 'confirmed')
            ->whereNull('reminder_sent_at') // Ещё не отправляли
            ->with(['slots', 'place', 'user'])
            ->get();

        $sent = 0;

        foreach ($bookings as $booking) {
            // Находим самый ранний слот
            $firstSlot = $booking->slots->sortBy('slot_datetime')->first();
            if (!$firstSlot || !$firstSlot->slot_datetime) continue;

            $slotTime = Carbon::parse($firstSlot->slot_datetime);
            $diffMinutes = $now->diffInMinutes($slotTime, false); // false = может быть отрицательным

            // Отправляем если до визита от 30 до 90 минут
            if ($diffMinutes >= 30 && $diffMinutes <= 90) {
                $email = $booking->getClientEmail();
                if (!$email) continue;

                try {
                    Mail::to($email)->send(new BookingReminder($booking));
                    $booking->update(['reminder_sent_at' => now()]);
                    $sent++;
                    $this->line("  → Напоминание отправлено: #{$booking->id} → {$email}");
                } catch (\Exception $e) {
                    $this->error("  ✕ Ошибка #{$booking->id}: " . $e->getMessage());
                }
            }
        }

        $this->info("Отправлено напоминаний: {$sent}");
        return 0;
    }
}