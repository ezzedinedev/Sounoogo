<?php

namespace App\Domain\Listening\Enums;

/** Thèmes proposés à l'ouverture d'une conversation (mêmes valeurs que l'app). */
enum Topic: string
{
    case Stress = 'stress';
    case Anxiety = 'anxiety';
    case Sadness = 'sadness';
    case Relationship = 'relationship';
    case Family = 'family';
    case Grief = 'grief';
    case Violence = 'violence';
    case SelfEsteem = 'self_esteem';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Stress => 'Stress',
            self::Anxiety => 'Anxiété',
            self::Sadness => 'Tristesse',
            self::Relationship => 'Relation',
            self::Family => 'Famille',
            self::Grief => 'Deuil',
            self::Violence => 'Violence',
            self::SelfEsteem => 'Estime de soi',
            self::Other => 'Autre',
        };
    }
}
