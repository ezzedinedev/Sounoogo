<?php

namespace App\Domain\Identity\Enums;

enum Role: string
{
    case Member = 'member';
    case Listener = 'listener';
    case Supervisor = 'supervisor';
    case Professional = 'professional';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::Member => 'Membre',
            self::Listener => 'Écoutant',
            self::Supervisor => 'Superviseur',
            self::Professional => 'Professionnel',
            self::Admin => 'Administrateur',
        };
    }

    /** Rôles qui peuvent accéder au back-office. */
    public function isStaff(): bool
    {
        return in_array($this, [self::Listener, self::Supervisor, self::Admin], true);
    }
}
