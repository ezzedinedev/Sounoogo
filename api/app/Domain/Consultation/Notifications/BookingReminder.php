<?php

namespace App\Domain\Consultation\Notifications;

use App\Domain\Consultation\Models\Booking;
use App\Domain\Identity\Models\User;
use App\Infrastructure\Push\FcmChannel;
use App\Infrastructure\Push\PushMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class BookingReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Booking $booking) {}

    /** @return list<class-string> Le canal vérifie lui-même la configuration et les appareils du membre. */
    public function via(User $notifiable): array
    {
        return [FcmChannel::class];
    }

    public function toPush(User $notifiable): PushMessage
    {
        $time = $this->booking->slot?->starts_at->format('H:i') ?? '';

        return new PushMessage(
            title: 'Rendez-vous à '.$time,
            body: 'Ta consultation commence dans une heure.',
            data: ['type' => 'booking', 'booking_id' => (string) $this->booking->id],
            collapseKey: 'booking-'.$this->booking->id,
        );
    }
}
