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

    /** Pour comparer deux niveaux. */
    public function weight(): int
    {
        return match ($this) {
            self::None => 0,
            self::Medium => 1,
            self::High => 2,
        };
    }
}
