<?php

namespace App\Domain\Listening\Data;

use App\Domain\Listening\Enums\RiskLevel;

final readonly class RiskAssessment
{
    public function __construct(
        public RiskLevel $level,
        /** Explication courte destinée au superviseur, sans donnée identifiante. */
        public string $reason,
    ) {}
}
