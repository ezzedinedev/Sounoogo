<?php

namespace App\Domain\Listening\Data;

final readonly class ConversationSummary
{
    /**
     * @param  list<string>  $watchPoints  Points de vigilance pour la suite de l'écoute.
     */
    public function __construct(
        public string $summary,
        public array $watchPoints = [],
    ) {}
}
