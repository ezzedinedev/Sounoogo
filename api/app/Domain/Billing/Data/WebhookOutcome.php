<?php

namespace App\Domain\Billing\Data;

use App\Domain\Billing\Enums\PaymentStatus;

final readonly class WebhookOutcome
{
    public function __construct(
        public PaymentStatus $status,
        public ?string $reference = null,
        public ?string $providerReference = null,
        public array $payload = [],
    ) {}
}
