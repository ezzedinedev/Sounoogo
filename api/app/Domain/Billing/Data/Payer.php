<?php

namespace App\Domain\Billing\Data;

/**
 * Identité transmise au prestataire. Pour préserver l'anonymat, on n'envoie que le pseudonyme ;
 * le numéro Mobile Money n'est jamais conservé par SUNOOGO.
 */
final readonly class Payer
{
    public function __construct(
        public string $name,
        public ?string $phone = null,
    ) {}
}
