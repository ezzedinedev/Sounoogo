<?php

namespace App\Domain\Billing\Exceptions;

use App\Domain\Billing\Enums\PaymentProvider;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PaymentProviderUnavailable extends HttpException
{
    public static function for(PaymentProvider $provider): self
    {
        return new self(503, "Le moyen de paiement {$provider->label()} n'est pas disponible pour le moment.");
    }
}
