<?php

namespace App\Domain\Content\Enums;

enum ExerciseCategory: string
{
    case Breathing = 'breathing';
    case Grounding = 'grounding';
    case Relaxation = 'relaxation';
    case Sleep = 'sleep';

    public function label(): string
    {
        return match ($this) {
            self::Breathing => 'Respiration',
            self::Grounding => 'Ancrage',
            self::Relaxation => 'Détente',
            self::Sleep => 'Sommeil',
        };
    }
}
