<?php

namespace App\Domain\Consultation\Enums;

enum ConsultationMode: string
{
    case InPerson = 'in_person';
    case Phone = 'phone';
    case Video = 'video';

    public function label(): string
    {
        return match ($this) {
            self::InPerson => 'Présentiel',
            self::Phone => 'Téléphone',
            self::Video => 'Visio',
        };
    }
}
