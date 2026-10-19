<?php

namespace App\Domain\Consultation\Enums;

enum BookingStatus: string
{
    case PendingPayment = 'pending_payment';
    case Confirmed = 'confirmed';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PendingPayment => 'En attente de paiement',
            self::Confirmed => 'Confirmée',
            self::Completed => 'Terminée',
            self::Cancelled => 'Annulée',
        };
    }
}
