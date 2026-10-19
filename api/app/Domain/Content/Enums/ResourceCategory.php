<?php

namespace App\Domain\Content\Enums;

enum ResourceCategory: string
{
    case HealthCenter = 'health_center';
    case MentalHealth = 'mental_health';
    case Association = 'association';
    case Helpline = 'helpline';
    case LegalAid = 'legal_aid';
    case SocialService = 'social_service';

    public function label(): string
    {
        return match ($this) {
            self::HealthCenter => 'Centre de santé',
            self::MentalHealth => 'Santé mentale',
            self::Association => 'Association',
            self::Helpline => 'Ligne d\'écoute',
            self::LegalAid => 'Aide juridique',
            self::SocialService => 'Action sociale',
        };
    }
}
