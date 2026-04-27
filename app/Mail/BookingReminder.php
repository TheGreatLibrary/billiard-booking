<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingReminder extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Booking $booking
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Напоминание: бронирование #' . $this->booking->id . ' через 1 час',
        );
    }

    public function content(): Content
    {
        $resources = $this->booking->getBookedResources();
        $uniqueSlots = $this->booking->slots->pluck('slot_time')->unique()->sort()->values();
        $slotDate = $this->booking->slots->first()?->slot_date;

        return new Content(
            view: 'emails.booking-reminder',
            with: [
                'booking' => $this->booking,
                'resources' => $resources,
                'slots' => $uniqueSlots,
                'date' => $slotDate,
                'place' => $this->booking->place,
                'clientName' => $this->booking->getClientName(),
            ],
        );
    }
}