<?php

namespace App\Domain\Listening\Enums;

enum ConversationStatus: string
{
    case Waiting = 'waiting';
    case Active = 'active';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Waiting => 'En attente',
            self::Active => 'En cours',
            self::Closed => 'Terminée',
        };
    }
}
