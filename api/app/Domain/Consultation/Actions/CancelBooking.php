<?php

namespace App\Domain\Consultation\Actions;

use App\Domain\Consultation\Enums\BookingStatus;
use App\Domain\Consultation\Models\Booking;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class CancelBooking
{
    public function __invoke(Booking $booking): Booking
    {
        if (! in_array($booking->status, [BookingStatus::PendingPayment, BookingStatus::Confirmed], true)) {
            throw ValidationException::withMessages(['booking' => 'Cette réservation ne peut plus être annulée.']);
        }

        return DB::transaction(function () use ($booking) {
            $booking->update(['status' => BookingStatus::Cancelled, 'cancelled_at' => now()]);
            $booking->slot?->update(['is_booked' => false]);

            return $booking;
        });
    }
}
