<?php

namespace App\Domain\Consultation\Contracts;

/** Service de visioconférence utilisé pour les téléconsultations. */
interface VideoConferencing
{
    public function isEnabled(): bool;

    /** Lien d'accès personnel à la salle. */
    public function joinUrl(string $room, string $displayName, bool $moderator): string;
}
