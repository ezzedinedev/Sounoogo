<?php

namespace App\Domain\Billing\Enums;

enum PaymentProvider: string
{
    case Mock = 'mock';
    case MoneyFusion = 'moneyfusion';
    case PayPal = 'paypal';

    public function label(): string
    {
        return match ($this) {
            self::Mock => 'Test',
            self::MoneyFusion => 'Mobile Money',
            self::PayPal => 'PayPal',
        };
    }

    /** Le prestataire débite un compte Mobile Money : il lui faut le numéro du payeur. */
    public function requiresPhone(): bool
    {
        return $this === self::MoneyFusion;
    }
}
