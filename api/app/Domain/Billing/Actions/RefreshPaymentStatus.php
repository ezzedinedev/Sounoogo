<?php

namespace App\Domain\Billing\Actions;

use App\Domain\Billing\Contracts\ChecksPaymentStatus;
use App\Domain\Billing\Exceptions\PaymentProviderUnavailable;
use App\Domain\Billing\Models\Payment;
use App\Infrastructure\Payments\PaymentGatewayManager;
use Illuminate\Support\Facades\Cache;

/**
 * Interroge le prestataire quand le membre suit un paiement encore en attente :
 * le paiement aboutit même si la notification du prestataire s'est perdue.
 */
final class RefreshPaymentStatus
{
    /** Une seule interrogation du prestataire par paiement sur cet intervalle. */
    private const THROTTLE_SECONDS = 10;

    public function __construct(
        private readonly PaymentGatewayManager $gateways,
        private readonly SettlePayment $settle,
    ) {}

    public function __invoke(Payment $payment): Payment
    {
        if ($payment->isFinal()) {
            return $payment;
        }

        try {
            $gateway = $this->gateways->for($payment->provider);
        } catch (PaymentProviderUnavailable) {
            return $payment;
        }

        if (! $gateway instanceof ChecksPaymentStatus || ! Cache::add("payments:refresh:{$payment->id}", true, self::THROTTLE_SECONDS)) {
            return $payment;
        }

        $outcome = $gateway->fetchStatus($payment);

        return $outcome ? ($this->settle)($payment, $outcome->status, $outcome->payload) : $payment;
    }
}
