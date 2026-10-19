<?php

namespace App\Domain\Listening\Enums;

enum AlertStatus: string
{
    case Open = 'open';
    case Acknowledged = 'acknowledged';
    case Resolved = 'resolved';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Ouverte',
            self::Acknowledged => 'Prise en charge',
            self::Resolved => 'Résolue',
        };
    }
}
