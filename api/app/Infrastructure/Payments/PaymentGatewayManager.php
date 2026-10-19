<?php

namespace App\Infrastructure\Payments;

use App\Domain\Billing\Contracts\PaymentGateway;
use App\Domain\Billing\Enums\PaymentProvider;
use App\Domain\Billing\Exceptions\PaymentProviderUnavailable;
use Illuminate\Contracts\Container\Container;

final class PaymentGatewayManager
{
    public function __construct(private readonly Container $container) {}

    public function for(PaymentProvider $provider): PaymentGateway
    {
        if (! in_array($provider, $this->available(), true)) {
            throw PaymentProviderUnavailable::for($provider);
        }

        return $this->container->make(match ($provider) {
            PaymentProvider::Mock => MockGateway::class,
            PaymentProvider::MoneyFusion => MoneyFusionGateway::class,
            PaymentProvider::PayPal => throw PaymentProviderUnavailable::for($provider),
        });
    }

    /** @return list<PaymentProvider> Moyens de paiement configurés, proposés au membre. */
    public function available(): array
    {
        $providers = [];

        if (filled(config('sunoogo.payments.moneyfusion.api_url'))) {
            $providers[] = PaymentProvider::MoneyFusion;
        }

        if (! app()->isProduction()) {
            $providers[] = PaymentProvider::Mock;
        }

        return $providers;
    }
}
