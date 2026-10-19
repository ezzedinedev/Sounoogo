<?php

namespace App\Domain\Billing\Contracts;

use App\Domain\Billing\Data\WebhookOutcome;
use App\Domain\Billing\Models\Payment;

/** Prestataire capable de donner l'état d'un paiement à la demande (filet de sécurité si une notification se perd). */
interface ChecksPaymentStatus
{
    public function fetchStatus(Payment $payment): ?WebhookOutcome;
}
