<?php

namespace App\Policies;

use App\Domain\Consultation\Models\Booking;
use App\Domain\Identity\Models\User;

class BookingPolicy
{
    public function view(User $user, Booking $booking): bool
    {
        return $this->isMember($user, $booking) || $this->isProfessional($user, $booking) || $user->can('bookings.manage');
    }

    public function pay(User $user, Booking $booking): bool
    {
        return $this->isMember($user, $booking);
    }

    public function cancel(User $user, Booking $booking): bool
    {
        return $this->isMember($user, $booking) || $this->isProfessional($user, $booking) || $user->can('bookings.manage');
    }

    public function complete(User $user, Booking $booking): bool
    {
        return $this->isProfessional($user, $booking);
    }

    private function isMember(User $user, Booking $booking): bool
    {
        return $booking->member_id === $user->id;
    }

    private function isProfessional(User $user, Booking $booking): bool
    {
        return $booking->professional?->user_id === $user->id;
    }
}
