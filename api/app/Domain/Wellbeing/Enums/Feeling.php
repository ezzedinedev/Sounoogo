<?php

namespace App\Domain\Wellbeing\Enums;

/** Émotions proposées lors du check-in (mêmes valeurs que l'app). */
enum Feeling: string
{
    case Calm = 'calm';
    case Joy = 'joy';
    case Gratitude = 'gratitude';
    case Tired = 'tired';
    case Anxious = 'anxious';
    case Sad = 'sad';
    case Angry = 'angry';
    case Lonely = 'lonely';
    case Stressed = 'stressed';

    public function label(): string
    {
        return match ($this) {
            self::Calm => 'Calme',
            self::Joy => 'Joie',
            self::Gratitude => 'Gratitude',
            self::Tired => 'Fatigue',
            self::Anxious => 'Anxiété',
            self::Sad => 'Tristesse',
            self::Angry => 'Colère',
            self::Lonely => 'Solitude',
            self::Stressed => 'Stress',
        };
    }
}
