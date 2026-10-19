<?php

namespace App\Console\Commands;

use App\Domain\Consultation\Enums\BookingStatus;
use App\Domain\Consultation\Models\Booking;
use App\Domain\Consultation\Notifications\BookingReminder;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('bookings:send-reminders')]
#[Description('Rappelle au membre sa consultation, une heure avant.')]
class SendBookingReminders extends Command
{
    public function handle(): int
    {
        $bookings = Booking::query()
            ->where('status', BookingStatus::Confirmed)
            ->whereNull('reminded_at')
            ->whereHas('slot', fn ($q) => $q->whereBetween('starts_at', [now(), now()->addHour()]))
            ->with(['member', 'slot'])
            ->get();

        foreach ($bookings as $booking) {
            $booking->member->notify(new BookingReminder($booking));
            $booking->update(['reminded_at' => now()]);
        }

        $this->info("{$bookings->count()} rappel(s) envoyé(s).");

        return self::SUCCESS;
    }
}
