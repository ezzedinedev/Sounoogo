<?php

namespace App\Domain\Billing\Enums;

enum PaymentProvider: string
{
    case Mock = 'mock';
    case Wave = 'wave';
    case OrangeMoney = 'orange_money';
    case PayPal = 'paypal';

    public function label(): string
    {
        return match ($this) {
            self::Mock => 'Test',
            self::Wave => 'Wave',
            self::OrangeMoney => 'Orange Money',
            self::PayPal => 'PayPal',
        };
    }
}
