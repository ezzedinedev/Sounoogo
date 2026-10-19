<?php

namespace App\Domain\Billing\Data;

final readonly class CheckoutSession
{
    public function __construct(
        public string $checkoutUrl,
        public ?string $providerReference = null,
    ) {}
}
