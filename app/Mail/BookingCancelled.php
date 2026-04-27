<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingCancelled extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Booking $booking,
        public string $reason = 'expired'
    ) {}

    public function envelope(): Envelope
    {
        $subjects = [
            'expired' => 'Бронирование #' . $this->booking->id . ' истекло',
            'canceled' => 'Бронирование #' . $this->booking->id . ' отменено',
            'refunded' => 'Возврат по бронированию #' . $this->booking->id,
        ];

        return new Envelope(
            subject: $subjects[$this->reason] ?? $subjects['canceled'],
        );
    }

    public function content(): Content
    {
        $resources = $this->booking->getBookedResources();
        $uniqueSlots = $this->booking->slots->pluck('slot_time')->unique()->sort()->values();
        $slotDate = $this->booking->slots->first()?->slot_date;

        return new Content(
            view: 'emails.booking-cancelled',
            with: [
                'booking' => $this->booking,
                'resources' => $resources,
                'slots' => $uniqueSlots,
                'date' => $slotDate,
                'place' => $this->booking->place,
                'clientName' => $this->booking->getClientName(),
                'reason' => $this->reason,
            ],
        );
    }
}