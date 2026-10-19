<?php

namespace App\Domain\Billing\Contracts;

use App\Domain\Billing\Models\Payment;

/** Ce qui peut être payé : une consultation, un abonnement, une formation. */
interface Payable
{
    public function paymentLabel(): string;

    /** Appelé une seule fois, quand le paiement est confirmé par le prestataire. */
    public function onPaymentSucceeded(Payment $payment): void;
}
