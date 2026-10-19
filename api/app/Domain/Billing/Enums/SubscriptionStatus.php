<?php

namespace App\Domain\Billing\Enums;

enum SubscriptionStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case Expired = 'expired';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'En attente',
            self::Active => 'Actif',
            self::Expired => 'Expiré',
            self::Cancelled => 'Annulé',
        };
    }
}
