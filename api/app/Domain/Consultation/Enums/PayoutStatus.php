<?php

namespace App\Domain\Consultation\Enums;

enum PayoutStatus: string
{
    case Requested = 'requested';
    case Paid = 'paid';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Requested => 'Demandé',
            self::Paid => 'Versé',
            self::Rejected => 'Refusé',
        };
    }
}
