<?php

namespace App\Domain\Billing\Enums;

enum PaymentStatus: string
{
    case Pending = 'pending';
    case Succeeded = 'succeeded';
    case Failed = 'failed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'En attente',
            self::Succeeded => 'Réussi',
            self::Failed => 'Échoué',
            self::Cancelled => 'Annulé',
        };
    }
}
