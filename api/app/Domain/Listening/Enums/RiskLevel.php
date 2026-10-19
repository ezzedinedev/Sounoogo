<?php

namespace App\Domain\Listening\Enums;

enum RiskLevel: string
{
    case None = 'none';
    case Medium = 'medium';
    case High = 'high';

    public function label(): string
    {
        return match ($this) {
            self::None => 'Aucun',
            self::Medium => 'Modéré',
            self::High => 'Élevé',
        };
    }
}
