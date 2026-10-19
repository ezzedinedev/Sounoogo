<?php

namespace App\Domain\Listening\Data;

final readonly class VoiceNote
{
    public function __construct(
        public string $path,
        public string $mime,
        /** Durée en secondes. */
        public int $duration,
    ) {}

    public function label(): string
    {
        return sprintf('Message vocal (%d:%02d)', intdiv($this->duration, 60), $this->duration % 60);
    }
}
