<?php

namespace App\Infrastructure\Push;

final readonly class PushMessage
{
    /**
     * @param  array<string, string>  $data  Données transmises à l'app (ouvrir le bon écran).
     */
    public function __construct(
        public string $title,
        public string $body,
        public array $data = [],
        /** Les notifications de même clé se remplacent au lieu de s'empiler. */
        public ?string $collapseKey = null,
    ) {}
}
