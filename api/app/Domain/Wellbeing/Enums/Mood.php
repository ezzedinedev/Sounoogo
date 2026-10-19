<?php

namespace App\Domain\Wellbeing\Enums;

enum Mood: int
{
    case VeryLow = 1;
    case Low = 2;
    case Neutral = 3;
    case Good = 4;
    case VeryGood = 5;

    public function label(): string
    {
        return match ($this) {
            self::VeryLow => 'Très difficile',
            self::Low => 'Difficile',
            self::Neutral => 'Moyen',
            self::Good => 'Bien',
            self::VeryGood => 'Très bien',
        };
    }

    public function isLow(): bool
    {
        return $this->value <= self::Low->value;
    }
}
