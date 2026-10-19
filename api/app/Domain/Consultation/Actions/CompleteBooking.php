<?php

namespace App\Domain\Consultation\Actions;

use App\Domain\Consultation\Enums\BookingStatus;
use App\Domain\Consultation\Models\Booking;

/** Clôture une consultation et fige la part du professionnel (prix moins la commission en vigueur). */
final class CompleteBooking
{
    public function __invoke(Booking $booking): Booking
    {
        abort_unless($booking->status === BookingStatus::Confirmed, 409, 'Seule une consultation confirmée peut être terminée.');

        $commission = max(0, min(100, (int) config('sunoogo.payouts.commission_percent')));

        $booking->update([
            'status' => BookingStatus::Completed,
            'completed_at' => now(),
            'professional_share' => intdiv($booking->price * (100 - $commission), 100),
        ]);

        return $booking;
    }
}
